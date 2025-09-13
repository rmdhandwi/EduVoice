<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue';

const toast = useToast();

const props = defineProps<{
    kuesioner: any;
    pertanyaans?: any[]; // kalau ada → mode edit (single/bulk), kalau tidak → mode create
    mode: 'create' | 'edit' | 'bulk';
    unsurs: any[];
}>();

// default opsi jawaban
const defaultOpsi = [
    { teks: 'Sangat Puas', skor: 4 },
    { teks: 'Puas', skor: 3 },
    { teks: 'Kurang Puas', skor: 2 },
    { teks: 'Tidak Puas', skor: 1 },
];

// isi form
// isi form
const form = useForm({
    pertanyaans: props.pertanyaans?.length
        ? JSON.parse(JSON.stringify(props.pertanyaans)) // clone deep untuk edit
        : props.unsurs.map((unsur) => ({
              teks: '',
              unsur: unsur.value, // otomatis isi sesuai unsur yang dikirim dari backend
              opsi: [...defaultOpsi],
          })),
});

// tambah pertanyaan baru
const addPertanyaan = () => {
    form.pertanyaans.push({
        teks: '',
        unsur: '',
        opsi: [...defaultOpsi],
    });
};

// hapus pertanyaan
const removePertanyaan = (i: number) => form.pertanyaans.splice(i, 1);

// tambah opsi
const addOpsi = (i: number) => {
    form.pertanyaans[i].opsi.push({ teks: '', skor: 0 });
};

// hapus opsi
const removeOpsi = (pi: number, oi: number) => {
    form.pertanyaans[pi].opsi.splice(oi, 1);
};

// error helper
const getError = (key: string) => (form.errors as Record<string, any>)[key] ?? null;

// validasi manual sebelum submit
const validateForm = () => {
    for (let i = 0; i < form.pertanyaans.length; i++) {
        const p = form.pertanyaans[i];

        // cek opsi minimal 1
        if (!p.opsi || p.opsi.length < 1) {
            toast.add({
                severity: 'error',
                summary: 'Validasi Gagal',
                detail: `Pertanyaan ${i + 1} : Minimal 1 opsi wajib diisi.`,
                life: 3000,
            });
            return false;
        }

        // cek tiap opsi
        for (let oi = 0; oi < p.opsi.length; oi++) {
            const o = p.opsi[oi];

            if (!o.teks || o.teks.trim() === '') {
                toast.add({
                    severity: 'error',
                    summary: 'Validasi Gagal',
                    detail: `Pertanyaan ${i + 1}, Opsi ${oi + 1}: Teks wajib diisi.`,
                    life: 3000,
                });
                return false;
            }

            if (!o.skor || o.skor < 1) {
                toast.add({
                    severity: 'error',
                    summary: 'Validasi Gagal',
                    detail: `Pertanyaan ${i + 1}, Opsi ${oi + 1}: Skor harus lebih dari 0.`,
                    life: 3000,
                });
                return false;
            }
        }
    }

    return true;
};

// submit handler
const submit = () => {
    if (!validateForm()) return;

    if (props.mode === 'create') {
        form.post(route('admin.kuesioner.pertanyaan.store', props.kuesioner.id));
    } else if (props.mode === 'edit') {
        const pertanyaan = form.pertanyaans[0]; // ambil item pertama
        form.transform((data) => ({
            teks: pertanyaan.teks,
            unsur: pertanyaan.unsur,
            opsi: pertanyaan.opsi,
        })).put(
            route('admin.kuesioner.pertanyaan.update', {
                kuesioner: props.kuesioner.id,
                pertanyaan: pertanyaan.id,
            }),
        );
    } else if (props.mode === 'bulk') {
        form.post(route('admin.kuesioner.pertanyaan.bulkUpdate', { kuesioner: props.kuesioner.id }));
    }
};
</script>

<template>
    <DashboardLayout
        :breadcrumb-items="[
            { label: 'Kuesioner', route: 'admin.kuesioner.index' },
            { label: kuesioner.judul },
            { label: mode === 'create' ? 'Tambah Pertanyaan' : mode === 'edit' ? 'Edit Pertanyaan' : 'Edit Banyak Pertanyaan' },
        ]"
        :title="mode === 'create' ? 'Tambah Pertanyaan' : mode === 'edit' ? 'Edit Pertanyaan' : 'Edit Banyak Pertanyaan'"
    >
        <Head :title="mode === 'create' ? 'Tambah Pertanyaan' : mode === 'edit' ? 'Edit Pertanyaan' : 'Edit Banyak Pertanyaan'" />

        <!-- Grid Pertanyaan -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3">
            <Card Card v-for="(p, i) in form.pertanyaans" :key="i">
                <template #content>
                    <!-- Pertanyaan -->
                    <div class="mb-3 flex flex-col gap-1">
                        <IftaLabel>
                            <Textarea
                                rows="3"
                                :invalid="!!getError(`pertanyaans.${i}.teks`)"
                                v-model="p.teks"
                                class="w-full"
                                placeholder="Tulis pertanyaan..."
                                style="resize: none"
                            />
                            <label class="text-sm font-medium">Teks Pertanyaan</label>
                        </IftaLabel>
                        <Message variant="simple" v-if="getError(`pertanyaans.${i}.teks`)" severity="error" size="small">
                            {{ getError(`pertanyaans.${i}.teks`) }}
                        </Message>
                    </div>

                    <!-- Unsur -->
                    <div class="mb-3 flex flex-col gap-1">
                        <IftaLabel>
                            <Select
                                v-model="p.unsur"
                                :invalid="!!getError(`pertanyaans.${i}.unsur`)"
                                :options="unsurs"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Pilih unsur"
                                class="mt-1 w-full"
                            />
                            <label class="text-sm font-medium">Unsur Pelayanan</label>
                        </IftaLabel>
                        <Message variant="simple" v-if="getError(`pertanyaans.${i}.unsur`)" severity="error" size="small">
                            {{ getError(`pertanyaans.${i}.unsur`) }}
                        </Message>
                    </div>

                    <!-- Grid Opsi -->
                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-500">Opsi Jawaban</label>
                        <div v-for="(o, oi) in p.opsi" :key="oi" class="mb-2 flex items-center gap-2">
                            <InputText v-model="o.teks" placeholder="Teks opsi" class="flex-1" />
                            <InputNumber v-model="o.skor" placeholder="Skor" inputClass="w-15" />
                            <Button size="small" icon="pi pi-trash" severity="danger" text @click.prevent="removeOpsi(i, oi)" />
                        </div>
                        <Button icon="pi pi-plus" label="Tambah Opsi" size="small" @click.prevent="addOpsi(i)" />
                    </div>

                    <!-- Aksi pertanyaan -->
                    <div class="flex justify-end">
                        <Button
                            size="small"
                            icon="pi pi-trash"
                            severity="danger"
                            @click.prevent="removePertanyaan(i)"
                            v-if="form.pertanyaans.length > 1 && mode !== 'edit'"
                        />
                    </div>
                </template>
            </Card>
        </div>

        <!-- Tombol tambah pertanyaan (hanya create/bulk) -->
        <div class="mt-4" v-if="mode !== 'edit'">
            <Button size="small" label="Tambah Pertanyaan Baru" icon="pi pi-plus" @click.prevent="addPertanyaan" />
        </div>

        <!-- Submit -->
        <div class="mt-6 flex gap-2">
            <Button size="small" severity="success" label="Simpan" :loading="form.processing" icon="pi pi-check" @click="submit" />
            <Button
                size="small"
                label="Batal"
                icon="pi pi-times"
                as="a"
                :href="route('admin.kuesioner.pertanyaan.index', kuesioner.id)"
                severity="danger"
            />
        </div>
    </DashboardLayout>
</template>
