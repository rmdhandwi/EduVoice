<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AppPageProps, Pertanyaan } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';

// Props inertia
const page = usePage<AppPageProps>();
const isEdit = !!page.props.kuesioner;

const toast = useToast();

// Title & Breadcrumb
const title = isEdit ? 'Edit Kuesioner' : 'Tambah Kuesioner';
const breadcrumb = [{ label: 'Kuesioner', route: 'kuesioner.index' }, { label: title }];

// 👇 bikin type khusus buat form
type KuesionerForm = {
    judul: string;
    deskripsi: string;
    pertanyaans: Pertanyaan[];
};

// Form inertia
const form = useForm<Record<string, any>>({
    judul: isEdit ? page.props.kuesioner?.judul || '' : '',
    deskripsi: isEdit ? page.props.kuesioner?.deskripsi || '' : '',
    pertanyaans: isEdit ? page.props.kuesioner?.pertanyaans || [] : [{ teks: '', tipe: 'text', opsi: [] }],
});

// Tambah pertanyaan baru
const addPertanyaan = () => {
    form.pertanyaans.push({ teks: '', tipe: 'text', opsi: [] });
};

// Hapus pertanyaan
const removePertanyaan = (index: number) => {
    form.pertanyaans.splice(index, 1);
};

// Tambah opsi jawaban
const addOpsi = (qIndex: number) => {
    form.pertanyaans[qIndex].opsi.push({ teks: '', skor: 0 });
};

// Hapus opsi jawaban
const removeOpsi = (qIndex: number, oIndex: number) => {
    form.pertanyaans[qIndex].opsi.splice(oIndex, 1);
};

// Submit
const submit = () => {
    // Validasi frontend: kalau radio/checkbox wajib punya opsi
    for (const [index, q] of form.pertanyaans.entries()) {
        if ((q.tipe === 'radio' || q.tipe === 'checkbox') && (!q.opsi || q.opsi.length === 0)) {
            toast.add({
                severity: 'warn',
                summary: 'Validasi Gagal',
                detail: `Pertanyaan ${index + 1} (${q.tipe}) harus memiliki minimal 1 opsi`,
                life: 3000,
            });
            return;
        }
    }

    if (isEdit) {
        form.put(route('admin.kuesioner.update', page.props.kuesioner?.id));
    } else {
        form.post(route('admin.kuesioner.store'));
    }
};
</script>

<template>
    <Head :title="title" />
    <DashboardLayout :breadcrumb-items="breadcrumb" :title="title">
        <div class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow dark:bg-gray-800">
            <!-- Judul & Deskripsi -->
            <div class="mb-8 space-y-4">
                <div class="flex flex-col gap-1">
                    <label class="text-dark text-sm font-semibold dark:text-gray-200">Judul Kuesioner</label>
                    <InputText v-model="form.judul" class="w-full" placeholder="Masukkan judul" :invalid="!!form.errors.judul" />
                    <Message v-if="form.errors.judul" severity="error" variant="simple" size="small">
                        {{ form.errors.judul }}
                    </Message>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-dark text-sm font-semibold dark:text-gray-200">Deskripsi</label>
                    <Textarea
                        v-model="form.deskripsi"
                        class="w-full"
                        placeholder="Masukkan deskripsi"
                        autoResize
                        :invalid="!!form.errors.deskripsi"
                    />
                    <Message v-if="form.errors.deskripsi" severity="error" variant="simple" size="small">
                        {{ form.errors.deskripsi }}
                    </Message>
                </div>
            </div>

            <!-- Pertanyaan dinamis -->
            <div class="mb-8 space-y-6">
                <div v-for="(q, qIndex) in form.pertanyaans" :key="qIndex">
                    <Card>
                        <template #title>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">Pertanyaan {{ qIndex + 1 }}</span>
                                <Button
                                    v-if="form.pertanyaans.length > 1"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    text
                                    size="small"
                                    @click="removePertanyaan(qIndex)"
                                />
                            </div>
                        </template>

                        <template #content>
                            <div class="space-y-4">
                                <!-- Teks pertanyaan -->
                                <div class="flex flex-col gap-1">
                                    <InputText
                                        v-model="q.teks"
                                        class="w-full"
                                        placeholder="Teks pertanyaan"
                                        :invalid="!!form.errors[`pertanyaans.${qIndex}.teks`]"
                                    />
                                    <Message v-if="form.errors[`pertanyaans.${qIndex}.teks`]" severity="error" variant="simple" size="small">
                                        {{ form.errors[`pertanyaans.${qIndex}.teks`] }}
                                    </Message>
                                </div>

                                <!-- Pilih tipe jawaban -->
                                <div class="flex flex-col gap-1">
                                    <Select
                                        v-model="q.tipe"
                                        :options="[
                                            { label: 'Text', value: 'text' },
                                            { label: 'Number', value: 'number' },
                                            { label: 'Date', value: 'date' },
                                            { label: 'Pilihan Ganda (Satu Pilihan)', value: 'radio' },
                                            { label: 'Centang (Lebih Dari Satu Pilihan)', value: 'checkbox' },
                                        ]"
                                        optionLabel="label"
                                        optionValue="value"
                                        placeholder="Pilih tipe jawaban"
                                        class="w-full"
                                        :invalid="!!form.errors[`pertanyaans.${qIndex}.tipe`]"
                                    />

                                    <Message v-if="form.errors[`pertanyaans.${qIndex}.tipe`]" severity="error" variant="simple" size="small">
                                        {{ form.errors[`pertanyaans.${qIndex}.tipe`] }}
                                    </Message>
                                </div>

                                <!-- Opsi jawaban -->
                                <div v-if="q.tipe === 'radio' || q.tipe === 'checkbox'" class="space-y-3">
                                    <!-- Radio (satu pilihan) -->
                                    <div v-for="(o, oIndex) in q.opsi" :key="oIndex" class="flex items-center gap-3">
                                        <!-- Input teks opsi -->
                                        <div class="flex flex-1 flex-col gap-1">
                                            <InputText
                                                v-model="o.teks"
                                                placeholder="Pilihan jawaban"
                                                :invalid="!!form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.teks`]"
                                            />
                                            <Message
                                                v-if="form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.teks`]"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                            >
                                                {{ form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.teks`] }}
                                            </Message>
                                        </div>

                                        <!-- Input skor -->
                                        <div class="flex flex-col gap-1">
                                            <InputNumber
                                                v-model="o.skor"
                                                placeholder="Skor"
                                                :invalid="!!form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.skor`]"
                                            />
                                            <Message
                                                v-if="form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.skor`]"
                                                severity="error"
                                                variant="simple"
                                                size="small"
                                            >
                                                {{ form.errors[`pertanyaans.${qIndex}.opsi.${oIndex}.skor`] }}
                                            </Message>
                                        </div>

                                        <!-- Hapus -->
                                        <Button icon="pi pi-trash" severity="danger" text size="small" @click="removeOpsi(qIndex, oIndex)" />
                                    </div>

                                    <!-- Tombol tambah opsi -->
                                    <Button icon="pi pi-plus" text size="small" label="Tambah Opsi" @click="addOpsi(qIndex)" />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

            <!-- Tombol tambah pertanyaan -->
            <div class="mb-8">
                <Button icon="pi pi-plus" size="small" label="Tambah Pertanyaan" @click="addPertanyaan" />
            </div>

            <!-- Pertanyaan statis -->
            <div class="mb-8">
                <Card>
                    <template #title>Kritik dan Saran</template>
                    <template #content>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Pertanyaan ini selalu ada sebagai input teks bebas.</p>
                    </template>
                </Card>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <div class="flex items-center justify-center gap-2">
                    <Button
                        label="Simpan"
                        size="small"
                        severity="success"
                        :disabled="form.processing"
                        :loading="form.processing"
                        icon="pi pi-check"
                        @click="submit"
                    />
                    <Button
                        label="Batal"
                        as="a"
                        :href="route('admin.kuesioner.index')"
                        size="small"
                        :disabled="form.processing"
                        severity="danger"
                        icon="pi pi-times"
                    />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
