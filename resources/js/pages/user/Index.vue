<script setup lang="ts">
import ManualStepper from '@/Components/ManualStepper.vue';
import { AppPageProps, KritikSaran } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';
import { onMounted, ref, watch } from 'vue';

const page = usePage<AppPageProps>();
const kuesioners = page.props.kuesioners;
const kuesioner = page.props.kuesioner;
const responden = page.props.responden;
const currentKuesionerId = page.props.currentKuesionerId;
const mode = page.props.mode;

const kritikSaranList = ref<KritikSaran[]>(
    (Array.isArray(page.props.kritik_saran) ? page.props.kritik_saran : []).map((r) => ({
        ...r,
        showKritikFull: false,
        showSaranFull: false,
    })),
);

const stepper = ref<InstanceType<typeof ManualStepper> | null>(null);
type FormErrors = typeof form.errors;

// Fungsi untuk cek apakah step sekarang valid
const isStepValid = (step: string) => {
    if (step === '1') {
        return (
            form.name.trim() !== '' &&
            form.jk !== '' &&
            form.umur !== null &&
            form.pekerjaan.trim() !== '' &&
            form.pendidikan_terakhir.trim() !== '' &&
            !form.errors.name &&
            !form.errors.jk &&
            !form.errors.umur &&
            !form.errors.pekerjaan &&
            !form.errors.pendidikan_terakhir
        );
    }
    if (step === '2') {
        return kuesioner?.pertanyaan?.every((p: any, idx: number) => {
            if (p.tipe === 'radio') {
                return form.jawaban[idx].opsi_id !== null && !form.errors[`jawaban.${idx}.opsi_id`];
            }
            if (p.tipe === 'checkbox') {
                return form.jawaban[idx].opsi_id.length > 0 && !form.errors[`jawaban.${idx}.opsi_id`];
            }
            if (p.tipe === 'text') {
                return form.jawaban[idx].teks.trim() !== '' && !form.errors[`jawaban.${idx}.teks`];
            }
            if (p.tipe === 'number') {
                return form.jawaban[idx].angka !== null && !form.errors[`jawaban.${idx}.angka`];
            }
            if (p.tipe === 'date') {
                return form.jawaban[idx].tanggal !== null && !form.errors[`jawaban.${idx}.tanggal`];
            }
            return true;
        });
    }
    if (step === '3') {
        return true; // kritik & saran opsional
    }
    return false;
};

// deteksi error step
const getErrorStep = (errors: FormErrors) => {
    for (const field in errors) {
        if (field.startsWith('jawaban.')) return 2;
        if (['name', 'jk', 'umur', 'pekerjaan', 'pendidikan_terakhir'].some((f) => field.startsWith(f))) return 1;
        if (['kritik', 'saran'].some((f) => field.startsWith(f))) return 3;
    }
    return null;
};

// sidebar state
const isSidebarOpen = ref(false);
const toggleSidebar = () => (isSidebarOpen.value = !isSidebarOpen.value);

// form state
const form = useForm({
    responden_id: responden?.id ?? null,
    kuesioner_id: kuesioner?.id ?? null,
    name: responden?.name ?? '',
    jk: responden?.jk ?? '',
    umur: responden?.umur ?? null,
    pekerjaan: responden?.pekerjaan ?? '',
    pendidikan_terakhir: responden?.pendidikan_terakhir ?? '',
    jawaban:
        kuesioner?.pertanyaan?.map((p: any) => ({
            pertanyaan_id: p.id,
            tipe: p.tipe,
            opsi_id: p.tipe === 'checkbox' ? [] : null,
            teks: '',
            angka: null,
            tanggal: null,
        })) ?? [],
    kritik: '',
    saran: '',
});

// toast notifikasi
const toast = useToast();

const showToast = (severity: 'success' | 'error', summary: string, detail: string) => {
    toast.add({ severity, summary, detail, life: 3000 });
};

// flash message dari laravel
onMounted(() => {
    if (page.props.flash?.success) showToast('success', 'Berhasil', page.props.flash.success);
    if (page.props.flash?.error) showToast('error', 'Gagal', page.props.flash.error);
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) showToast('success', 'Berhasil', flash.success);
        if (flash?.error) showToast('error', 'Gagal', flash.error);
    },
    { deep: true },
);

const submitForm = () => {
    const role = page.props.auth?.user?.role ?? '';

    if (mode === 'create') {
        form.post(route('user.kuesioner.submit', kuesioner?.id), {
            preserveScroll: true,
            onError: () => {
                const stepError = getErrorStep(form.errors);
                if (stepError) {
                    localStorage.removeItem('currentStep');
                    stepper.value?.goTo(stepError.toString());
                }
            },
        });
    } else {
        if (role === 'admin') {
            form.put(route('admin.kuesioner.update', responden?.id), { preserveScroll: true });
        } else {
            form.put(route('admin.kuesioner.show', responden?.id), { preserveScroll: true });
        }
    }
};

function toggleKritik(id: number | string) {
    const item = kritikSaranList.value.find((r) => r.id === id);
    if (item) item.showKritikFull = !item.showKritikFull;
}

function toggleSaran(id: number | string) {
    const item = kritikSaranList.value.find((r) => r.id === id);
    if (item) item.showSaranFull = !item.showSaranFull;
}
</script>

<template>
    <Toast />
    <Head title="Survey Kepuasan Masyarakat" />
    <div
        class="flex h-screen bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 text-gray-900 dark:bg-gray-950 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 dark:text-gray-100"
    >
        <!-- Sidebar (desktop) -->
        <aside class="hidden w-64 flex-shrink-0 bg-white/80 shadow-md backdrop-blur-md md:flex md:flex-col dark:border-gray-700 dark:bg-gray-900/90">
            <div class="flex items-center gap-3 p-4">
                <img src="../../../../public/img/logo_kota.png" alt="Logo" class="h-10 w-10" />
                <span class="text-lg font-bold">SKM DISDIKBUD</span>
            </div>

            <!-- Scrollable menu -->
            <nav class="flex-1 space-y-1 overflow-y-auto px-2">
                <a
                    v-for="item in kuesioners"
                    :key="item.id"
                    :href="route('user.kuesioner.show', item.id)"
                    class="block truncate rounded px-3 py-2 text-sm transition-all duration-300"
                    :class="[
                        currentKuesionerId === item.id
                            ? 'bg-gradient-to-r from-blue-500 to-purple-500 font-semibold text-white shadow-md'
                            : 'hover:bg-blue-100 dark:hover:bg-gray-700',
                    ]"
                >
                    {{ item.judul }}
                </a>
            </nav>

            <!-- Tentang Kami & Kritik Saran -->
            <div class="flex flex-col gap-2 border-t border-gray-200 p-4 dark:border-gray-700">
                <a
                    href="https://dinaspendidikankotajayapura.co.id/"
                    target="_blank"
                    class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    <i class="pi pi-info-circle"></i>
                    <span>Tentang Kami</span>
                </a>

                <a
                    :href="route('user.kritik.saran')"
                    :class="[
                        mode === 'kritik'
                            ? 'bg-gradient-to-r from-blue-500 to-purple-500 font-semibold text-white shadow-md'
                            : 'hover:bg-blue-100 dark:hover:bg-gray-700',
                    ]"
                    class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    <i class="pi pi-comment"></i>
                    <span>Kritik & Saran</span>
                </a>

                <Button
                    unstyled
                    as="a"
                    :href="route('login')"
                    class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    <i class="pi pi-sign-in"></i>
                    <span>Login</span>
                </Button>
            </div>
        </aside>

        <!-- Mobile drawer -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="-translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="-translate-x-full opacity-0"
        >
            <div v-if="isSidebarOpen" class="fixed inset-0 z-40 flex md:hidden">
                <!-- overlay -->
                <div class="fixed inset-0 bg-black/50" @click="toggleSidebar"></div>
                <!-- sidebar -->
                <aside
                    class="relative z-50 flex w-64 flex-shrink-0 flex-col border-r bg-white/90 shadow-lg backdrop-blur-lg dark:border-gray-700 dark:bg-gray-900/90"
                >
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center gap-2">
                            <img src="../../../../public/img/logo_kota.png" alt="Logo" class="h-8 w-8" />
                            <span class="text-lg font-bold">Pilih Kuesioner</span>
                        </div>
                        <button size="small" class="rounded px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-800" @click="toggleSidebar">✕</button>
                    </div>

                    <!-- Scrollable menu -->
                    <nav class="flex-1 space-y-1 overflow-y-auto px-2">
                        <a
                            v-for="item in kuesioners"
                            :key="item.id"
                            :href="route('user.kuesioner.show', item.id)"
                            class="block truncate rounded px-3 py-2 text-sm transition-all duration-300"
                            :class="[
                                currentKuesionerId === item.id
                                    ? 'bg-gradient-to-r from-blue-500 to-purple-500 font-semibold text-white shadow-md'
                                    : 'hover:bg-blue-100 dark:hover:bg-gray-700',
                            ]"
                            @click="toggleSidebar"
                        >
                            {{ item.judul }}
                        </a>
                    </nav>

                    <!-- Tentang Kami & Kritik Saran -->
                    <div class="flex flex-col gap-2 border-t border-gray-200 p-4 dark:border-gray-700">
                        <a
                            href="https://dinaspendidikankotajayapura.co.id/"
                            target="_blank"
                            class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <i class="pi pi-info-circle"></i>
                            <span>Tentang Kami</span>
                        </a>

                        <a
                            :href="route('user.kritik.saran')"
                            :class="[
                                mode === 'kritik'
                                    ? 'bg-gradient-to-r from-blue-500 to-purple-500 font-semibold text-white shadow-md'
                                    : 'hover:bg-blue-100 dark:hover:bg-gray-700',
                            ]"
                            class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <i class="pi pi-comment"></i>
                            <span>Kritik & Saran</span>
                        </a>

                        <Button
                            unstyled
                            as="a"
                            :href="route('login')"
                            class="flex items-center gap-2 rounded px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            <i class="pi pi-sign-in"></i>
                            <span>Login</span>
                        </Button>
                    </div>
                </aside>
            </div>
        </transition>

        <!-- Main -->
        <main class="flex-1 overflow-y-auto p-6">
            <!-- Mobile topbar -->
            <div class="mb-4 flex items-center justify-between md:hidden">
                <div class="flex items-center gap-2">
                    <img src="../../../../public/img/logo_kota.png" alt="Logo" class="h-8 w-8" />
                    <h1 class="text-lg font-bold">SKM DISDIKBUD</h1>
                </div>
                <button
                    size="small"
                    @click="toggleSidebar"
                    class="rounded bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-md hover:bg-blue-700"
                >
                    Pilih Kuesioner
                </button>
            </div>

            <div v-if="mode === 'kritik'">
                <h1 class="mb-6 bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-center text-3xl font-extrabold text-transparent">
                    Kritik & Saran Responden
                </h1>

                <div v-if="kritikSaranList.length" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="r in kritikSaranList"
                        :key="r.id"
                        class="group relative rounded-xl border border-gray-200 bg-white p-5 shadow-md transition hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
                    >
                        <!-- Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Avatar -->
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 font-bold text-white shadow"
                                >
                                    {{ r.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-100">
                                        {{ r.name }}
                                        <span class="ml-1 text-sm text-gray-400">({{ r.jk }})</span>
                                    </h4>
                                </div>
                            </div>
                            <span v-if="r.created_at" class="text-xs text-gray-400 dark:text-gray-500">
                                {{
                                    new Date(r.created_at).toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                    })
                                }}
                            </span>
                        </div>

                        <!-- Kritik -->
                        <div v-if="r.kritik" class="mt-3">
                            <span class="mb-1 block text-xs font-bold text-red-500">Kritik</span>
                            <p class="relative text-sm text-gray-700 dark:text-gray-300" :class="{ 'line-clamp-3': !r.showKritikFull }">
                                {{ r.kritik }}
                            </p>
                            <button
                                v-if="r.kritik.length > 120"
                                @click="toggleKritik(r.id)"
                                class="mt-1 text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-400"
                            >
                                {{ r.showKritikFull ? 'Sembunyikan' : 'Selengkapnya' }}
                            </button>
                        </div>

                        <!-- Saran -->
                        <div v-if="r.saran" class="mt-3">
                            <span class="mb-1 block text-xs font-bold text-green-500">Saran</span>
                            <p class="relative text-sm text-gray-700 dark:text-gray-300" :class="{ 'line-clamp-3': !r.showSaranFull }">
                                {{ r.saran }}
                            </p>
                            <button
                                v-if="r.saran.length > 120"
                                @click="toggleSaran(r.id)"
                                class="mt-1 text-xs font-semibold text-indigo-600 hover:underline dark:text-indigo-400"
                            >
                                {{ r.showSaranFull ? 'Sembunyikan' : 'Selengkapnya' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center text-gray-500 dark:text-gray-300">Belum ada kritik & saran</div>
            </div>

            <div v-else-if="!kuesioner && mode === 'responden'" class="flex h-full items-center justify-center text-gray-500 dark:text-gray-400">
                <!-- Text -->
                <span class="relative z-10">Silahkan pilih Kuesioner</span>
            </div>

            <div v-else>
                <h1 class="mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-center text-3xl font-extrabold text-transparent">
                    {{ kuesioner.judul }}
                </h1>
                <p class="mb-6 text-center text-gray-600 dark:text-gray-300">
                    {{ kuesioner.deskripsi }}
                </p>

                <div class="card mx-auto max-w-3xl bg-white p-6 shadow-md dark:bg-gray-800">
                    <ManualStepper
                        ref="stepper"
                        :steps="[
                            { value: '1', label: 'Data Responden' },
                            { value: '2', label: 'Pertanyaan' },
                            { value: '3', label: 'Kritik & Saran' },
                        ]"
                    >
                        <template #default="{ step, goTo }">
                            <!-- Step 1 -->
                            <div v-if="step === '1'" class="space-y-6">
                                <div class="flex flex-col gap-1">
                                    <IftaLabel>
                                        <InputText
                                            id="name"
                                            v-model="form.name"
                                            :invalid="!!form.errors.name"
                                            class="w-full"
                                            placeholder="Masukkan Nama"
                                            variant="filled"
                                        />
                                        <label for="name">Nama</label>
                                    </IftaLabel>
                                    <Message v-if="form.errors.name" size="small" variant="simple" severity="error">{{ form.errors.name }}</Message>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label>Jenis Kelamin</label>
                                    <div class="mt-1 flex flex-col gap-4">
                                        <div class="flex gap-2">
                                            <RadioButton inputId="jkL" value="Laki-Laki" v-model="form.jk" :invalid="!!form.errors.jk" />
                                            <label for="jkL">Laki-Laki</label>
                                        </div>
                                        <div class="flex gap-2">
                                            <RadioButton inputId="jkP" value="Perempuan" v-model="form.jk" :invalid="!!form.errors.jk" />
                                            <label for="jkP">Perempuan</label>
                                        </div>
                                    </div>
                                    <Message v-if="form.errors.jk" size="small" variant="simple" severity="error">{{ form.errors.jk }}</Message>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <IftaLabel>
                                        <InputNumber
                                            v-model="form.umur"
                                            :invalid="!!form.errors.umur"
                                            class="w-full"
                                            placeholder="Masukkan Umur"
                                            variant="filled"
                                        />
                                        <label>Umur</label>
                                    </IftaLabel>
                                    <Message v-if="form.errors.umur" size="small" variant="simple" severity="error">{{ form.errors.umur }}</Message>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <IftaLabel>
                                        <InputText
                                            v-model="form.pekerjaan"
                                            :invalid="!!form.errors.pekerjaan"
                                            class="w-full"
                                            placeholder="Masukkan Pekerjaan"
                                            variant="filled"
                                        />
                                        <label>Pekerjaan</label>
                                    </IftaLabel>
                                    <Message v-if="form.errors.pekerjaan" size="small" variant="simple" severity="error">
                                        {{ form.errors.pekerjaan }}
                                    </Message>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <IftaLabel>
                                        <InputText
                                            v-model="form.pendidikan_terakhir"
                                            :invalid="!!form.errors.pendidikan_terakhir"
                                            class="w-full"
                                            placeholder="Masukkan Pendidikan Terakhir"
                                            variant="filled"
                                        />
                                        <label>Pendidikan Terakhir</label>
                                    </IftaLabel>
                                    <Message v-if="form.errors.pendidikan_terakhir" size="small" variant="simple" severity="error">
                                        {{ form.errors.pendidikan_terakhir }}
                                    </Message>
                                </div>

                                <div class="flex justify-end border-t border-gray-300 pt-6 dark:border-gray-600">
                                    <Button
                                        size="small"
                                        label="Selanjutnya"
                                        icon="pi pi-arrow-right"
                                        @click="goTo('2')"
                                        :disabled="!isStepValid('1')"
                                        class="transition disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div v-if="step === '2'" class="flex h-[70vh] flex-col">
                                <!-- Daftar pertanyaan -->
                                <div class="flex-1 space-y-6 overflow-y-auto pr-2">
                                    <div
                                        v-for="(pertanyaan, idx) in kuesioner.pertanyaan"
                                        :key="pertanyaan.id"
                                        class="border-b border-gray-300 pb-4 last:border-none dark:border-gray-600"
                                    >
                                        <p class="mb-2 font-medium">{{ idx + 1 }}. {{ pertanyaan.teks }}</p>

                                        <!-- Radio -->
                                        <div v-if="pertanyaan.tipe === 'radio'" class="flex flex-col gap-1">
                                            <div v-for="opsi in pertanyaan.opsi_jawaban" :key="opsi.id" class="flex items-center gap-2">
                                                <RadioButton
                                                    :inputId="`opsi-${opsi.id}`"
                                                    :value="opsi.id"
                                                    :invalid="!!form.errors[`jawaban.${idx}.opsi_id`]"
                                                    v-model="form.jawaban[idx].opsi_id"
                                                />
                                                <label :for="`opsi-${opsi.id}`">{{ opsi.teks }}</label>
                                            </div>
                                            <Message v-if="form.errors[`jawaban.${idx}.opsi_id`]" size="small" variant="simple" severity="error">
                                                {{ form.errors[`jawaban.${idx}.opsi_id`] }}
                                            </Message>
                                        </div>

                                        <!-- Checkbox -->
                                        <div v-else-if="pertanyaan.tipe === 'checkbox'" class="flex flex-col gap-1">
                                            <div v-for="opsi in pertanyaan.opsi_jawaban" :key="opsi.id" class="flex items-center gap-2">
                                                <Checkbox
                                                    :inputId="`opsi-${opsi.id}`"
                                                    :value="opsi.id"
                                                    :invalid="!!form.errors[`jawaban.${idx}.opsi_id`]"
                                                    v-model="form.jawaban[idx].opsi_id"
                                                />
                                                <label :for="`opsi-${opsi.id}`">{{ opsi.teks }}</label>
                                            </div>
                                            <Message v-if="form.errors[`jawaban.${idx}.opsi_id`]" size="small" severity="error" variant="simple">
                                                {{ form.errors[`jawaban.${idx}.opsi_id`] }}
                                            </Message>
                                        </div>

                                        <!-- Text -->
                                        <div v-else-if="pertanyaan.tipe === 'text'" class="flex flex-col gap-1">
                                            <InputText
                                                v-model="form.jawaban[idx].teks"
                                                class="w-full"
                                                placeholder="Masukkan Jawaban"
                                                :invalid="!!form.errors[`jawaban.${idx}.teks`]"
                                            />
                                            <Message v-if="form.errors[`jawaban.${idx}.teks`]" size="small" variant="simple" severity="error">
                                                {{ form.errors[`jawaban.${idx}.teks`] }}
                                            </Message>
                                        </div>

                                        <!-- Number -->
                                        <div v-else-if="pertanyaan.tipe === 'number'" class="flex flex-col gap-1">
                                            <InputNumber
                                                v-model="form.jawaban[idx].angka"
                                                placeholder="Masukkan Angka"
                                                class="w-full"
                                                :invalid="!!form.errors[`jawaban.${idx}.angka`]"
                                            />
                                            <Message v-if="form.errors[`jawaban.${idx}.angka`]" size="small" variant="simple" severity="error">
                                                {{ form.errors[`jawaban.${idx}.angka`] }}
                                            </Message>
                                        </div>

                                        <!-- Date -->
                                        <div v-else-if="pertanyaan.tipe === 'date'" class="flex flex-col gap-1">
                                            <DatePicker
                                                v-model="form.jawaban[idx].tanggal"
                                                placeholder="Pilih Tanggal"
                                                class="w-full"
                                                :invalid="!!form.errors[`jawaban.${idx}.tanggal`]"
                                            />
                                            <Message v-if="form.errors[`jawaban.${idx}.tanggal`]" size="small" variant="simple" severity="error">
                                                {{ form.errors[`jawaban.${idx}.tanggal`] }}
                                            </Message>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol navigasi selalu fixed di bawah -->
                                <div class="mt-4 flex justify-between border-t border-gray-300 pt-4 dark:border-gray-600">
                                    <Button size="small" label="Sebelumnya" severity="secondary" icon="pi pi-arrow-left" @click="goTo('1')" />
                                    <Button
                                        size="small"
                                        label="Selanjutnya"
                                        icon="pi pi-arrow-right"
                                        @click="goTo('3')"
                                        :disabled="!isStepValid('2')"
                                        class="transition disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div v-if="step === '3'" class="space-y-4">
                                <IftaLabel>
                                    <Textarea v-model="form.kritik" class="w-full" rows="4" />
                                    <label>Kritik</label>
                                </IftaLabel>
                                <IftaLabel>
                                    <Textarea v-model="form.saran" class="w-full" rows="4" />
                                    <label>Saran</label>
                                </IftaLabel>

                                <div class="flex justify-between border-t border-gray-300 pt-6 dark:border-gray-600">
                                    <Button size="small" label="Sebelumnya" severity="secondary" icon="pi pi-arrow-left" @click="goTo('2')" />
                                    <Button
                                        size="small"
                                        label="Kirim"
                                        icon="pi pi-send"
                                        @click="submitForm"
                                        :loading="form.processing"
                                        :disabled="!isStepValid('3')"
                                        class="transition disabled:cursor-not-allowed disabled:opacity-50"
                                    />
                                </div>
                            </div>
                        </template>
                    </ManualStepper>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.grid > div {
    animation: fade-in-up 0.6s ease-out both;
}

.grid > div:hover {
    transform: scale(1.05);
    z-index: 50;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
}
</style>
