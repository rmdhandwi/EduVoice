<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AppPageProps } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { FileText } from 'lucide-vue-next';
import { useConfirm, useToast } from 'primevue';
import { computed, ref } from 'vue';

const page = usePage<AppPageProps>();
const toast = useToast();
const confirm = useConfirm();

const kuesioners = computed(() => page.props.kuesioners);

const breadcrumb = [{ label: 'Kuesioner' }];

// === Modal State ===
const showModal = ref(false);
const isEdit = ref(false);
const form = useForm({
    id: null,
    judul: '',
    deskripsi: '',
});

// Buka modal untuk tambah
const openCreate = () => {
    isEdit.value = false;
    form.reset();
    showModal.value = true;
};

// Buka modal untuk edit
const openEdit = (k: any) => {
    isEdit.value = true;
    form.id = k.id;
    form.judul = k.judul;
    form.deskripsi = k.deskripsi;
    showModal.value = true;
};

// Simpan form
const saveForm = () => {
    if (isEdit.value) {
        form.put(route('admin.kuesioner.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.clearErrors;
                form.reset;

                // Reload setelah toast selesai
                setTimeout(() => {
                    window.location.reload();
                }, 2000); // sama dengan life di atas
            },
            onError: () => {
                showModal.value = true;
            },
        });
    } else {
        form.post(route('admin.kuesioner.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.clearErrors;
                form.reset;

                // Reload setelah toast selesai
                setTimeout(() => {
                    window.location.reload();
                }, 2000); // sama dengan life di atas
            },
            onError: () => {
                showModal.value = true;
            },
        });
    }
};

// Hapus kuesioner
const deleteOne = (id: number) => {
    confirm.require({
        group: 'deleteConfirm',
        header: 'Konfirmasi Hapus',
        message: 'Apakah kamu yakin ingin menghapus kuesioner ini?',
        accept: () => {
            router.delete(route('admin.kuesioner.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    // Reload setelah toast selesai
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000); // sama dengan life di atas
                },
            });
        },
        reject: () => {
            toast.add({
                severity: 'info',
                summary: 'Dibatalkan',
                detail: 'Penghapusan dibatalkan',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <!-- Dialog konfirmasi hapus -->
    <ConfirmDialog group="deleteConfirm">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="bg-surface-0 dark:bg-surface-900 flex flex-col items-center rounded p-8">
                <div class="-mt-20 inline-flex h-24 w-24 items-center justify-center rounded-full bg-red-500 text-white">
                    <i class="pi pi-trash !text-4xl"></i>
                </div>
                <span class="mt-6 mb-2 block text-2xl font-bold">{{ message.header }}</span>
                <p class="mb-0">{{ message.message }}</p>
                <div class="mt-6 flex items-center gap-2">
                    <Button size="small" label="Ya, Hapus" severity="danger" @click="acceptCallback" />
                    <Button size="small" label="Batal" variant="outlined" @click="rejectCallback" />
                </div>
            </div>
        </template>
    </ConfirmDialog>

    <!-- Modal Buat/Edit -->
    <Dialog v-model:visible="showModal" :header="isEdit ? 'Edit Kuesioner' : 'Buat Kuesioner'" modal class="w-full max-w-lg">
        <div class="flex flex-col gap-4">
            <!-- Judul -->
            <div>
                <label for="judul" class="mb-1 block text-sm font-medium">Judul</label>
                <InputText id="judul" v-model="form.judul" class="w-full" placeholder="Masukkan judul kuesioner" />
                <small v-if="form.errors.judul" class="text-red-500">{{ form.errors.judul }}</small>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="mb-1 block text-sm font-medium">Deskripsi</label>
                <Textarea id="deskripsi" v-model="form.deskripsi" class="w-full" rows="3" placeholder="Masukkan deskripsi (opsional)" />
                <small v-if="form.errors.deskripsi" class="text-red-500">{{ form.errors.deskripsi }}</small>
            </div>
        </div>

        <template #footer>
            <Button size="small" label="Simpan" severity="success" icon="pi pi-check" :loading="form.processing" @click="saveForm" />
            <Button size="small" icon="pi pi-times" severity="danger" label="Batal" variant="outlined" @click="showModal = false" />
        </template>
    </Dialog>

    <DashboardLayout :breadcrumb-items="breadcrumb" title="Kelola Kuesioner">
        <Head title="Kuesioner" />

        <!-- Header actions -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Button size="small" label="Buat Kuesioner Baru" icon="pi pi-plus" @click="openCreate" />
        </div>

        <!-- Grid Cards -->
        <div class="animate-fade-in grid gap-6 delay-300 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="k in kuesioners"
                :key="k.id"
                class="group relative transform rounded-2xl bg-white p-6 shadow-lg transition hover:scale-[1.02] hover:shadow-xl dark:bg-gray-800"
            >
                <!-- Action buttons -->
                <div class="absolute top-4 right-4 flex gap-2">
                    <Button icon="pi pi-pencil" size="small" rounded variant="text" aria-label="Edit" @click="openEdit(k)" />
                    <Button icon="pi pi-trash" variant="text" severity="danger" aria-label="Hapus" size="small" @click="deleteOne(k.id)" rounded />
                </div>

                <!-- Icon -->
                <Button
                    unstyled
                    as="a"
                    @click="router.visit(route('admin.kuesioner.pertanyaan.index', k.id))"
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 transition hover:scale-[1.02] hover:cursor-pointer"
                >
                    <FileText class="h-6 w-6" />
                </Button>

                <!-- Judul -->
                <h3
                    class="mb-2 cursor-pointer text-lg font-bold text-gray-800 transition hover:text-indigo-600 dark:text-gray-100"
                    @click="router.visit(route('admin.kuesioner.show', k.id))"
                >
                    {{ k.judul }}
                </h3>

                <!-- Deskripsi -->
                <p class="line-clamp-3 text-sm text-gray-600 dark:text-gray-300">
                    {{ k.deskripsi || 'Tidak ada deskripsi.' }}
                </p>
            </div>
        </div>

        <!-- Kosong -->
        <!-- Empty State -->
        <div
            v-if="!kuesioners.length"
            class="flex flex-col items-center animate-fade-in delay-300 justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 py-20 text-center text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mb-4 h-14 w-14 text-gray-400 dark:text-gray-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 17v-2a4 4 0 00-4-4H3m6 6h6m0 0v-2a4 4 0 014-4h2m-6 6v2m0-2H9"
                />
            </svg>
            <p class="text-base font-semibold">Belum ada data Kuesioner tersedia</p>
            <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">Silakan tambahkan data kuesioner</p>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.8s ease-out both;
}
.delay-100 {
    animation-delay: 0.1s;
}
.delay-200 {
    animation-delay: 0.2s;
}
.delay-300 {
    animation-delay: 0.3s;
}
</style>
