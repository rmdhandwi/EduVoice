<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AppPageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { useConfirm, useToast } from 'primevue';
import { computed, ref } from 'vue';

// Props dari backend
const page = usePage<AppPageProps>();
const toast = useToast();
const confirm = useConfirm();

// Data kuesioner
const kuesioners = computed(() => page.props.kuesioners);

// State untuk DataTable
const selectedKuesioners = ref<any[]>([]);

const breadcrumb = [{ label: 'Kuesioner' }];

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Hapus satu
const deleteOne = (id: number) => {
    confirm.require({
        group: 'deleteConfirm',
        header: 'Konfirmasi Hapus',
        message: 'Apakah kamu yakin ingin menghapus kuesioner ini?',
        accept: () => {
            router.delete(route('admin.kuesioner.destroy', id), {
                preserveScroll: true,
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

// Hapus banyak
const deleteSelected = () => {
    if (!selectedKuesioners.value.length) return;

    confirm.require({
        group: 'deleteConfirm',
        header: 'Konfirmasi Hapus Banyak',
        message: `Apakah kamu yakin ingin menghapus ${selectedKuesioners.value.length} kuesioner terpilih?`,
        accept: () => {
            const ids = selectedKuesioners.value.map((k) => k.id);

            router.post(
                route('admin.kuesioner.bulkDestroy'),
                { ids },
                {
                    preserveScroll: true,
                    onSuccess: () => (selectedKuesioners.value = []),
                },
            );
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
                    <Button label="Ya, Hapus" severity="danger" @click="acceptCallback" />
                    <Button label="Batal" variant="outlined" @click="rejectCallback" />
                </div>
            </div>
        </template>
    </ConfirmDialog>

    <DashboardLayout :breadcrumb-items="breadcrumb" title="Kelola Kuesioner">
        <Head title="Kuesioner" />
        <div class="mx-auto rounded-xl bg-white p-6 shadow dark:bg-gray-800">
            <!-- Header actions -->
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <!-- Kiri: tombol tambah -->
                <Button
                    label="Buat Kuesioner Baru"
                    as="a"
                    :href="route('admin.kuesioner.create')"
                    icon="pi pi-plus"
                    severity="success"
                    size="small"
                />

                <!-- Kanan: search -->
                <div class="flex w-full sm:w-64">
                    <IconField class="w-full">
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Cari kuesioner..." class="w-full" />
                    </IconField>
                </div>
            </div>

            <!-- Bulk actions -->
            <div v-if="selectedKuesioners.length > 0" class="mb-4 flex items-center justify-between rounded-lg bg-red-50 p-3 text-red-700">
                <span>{{ selectedKuesioners.length }} kuesioner dipilih</span>
                <Button icon="pi pi-trash" label="Hapus Terpilih" size="small" severity="danger" @click="deleteSelected" />
            </div>

            <!-- Tabel -->
            <Card class="mb-4">
                <template #content>
                    <DataTable
                        :value="kuesioners"
                        v-model:selection="selectedKuesioners"
                        dataKey="id"
                        selectionMode="checkbox"
                        paginator
                        size="small"
                        :rows="10"
                        :rowsPerPageOptions="[5, 10, 20, 50]"
                        responsiveLayout="scroll"
                        
                        resizableColumns
                        columnResizeMode="expand"
                        scrollHeight="600px"
                        :filters="filters"
                    >
                        <template #empty>
                            <div class="flex flex-col items-center justify-center p-6">
                                <i class="pi pi-info-circle !text-2xl text-gray-400"></i>
                                <p class="mt-4 text-gray-600 dark:text-gray-300">Tidak ada kuesioner yang tersedia.</p>
                            </div>
                        </template>

                        <!-- Checkbox -->
                        <Column selectionMode="multiple" headerStyle="width: 3rem" />

                        <!-- Judul -->
                        <Column field="judul" header="Judul" sortable />

                        <!-- Deskripsi -->
                        <Column field="deskripsi" header="Deskripsi" />

                        <!-- Tanggal dibuat -->
                        <Column field="created_at" header="Dibuat" sortable>
                            <template #body="{ data }">
                                {{
                                    new Date(data.created_at).toLocaleDateString('id-ID', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                    })
                                }}
                            </template>
                        </Column>

                        <!-- Action -->
                        <Column header="Aksi" style="width: 100px">
                            <template #body="{ data }">
                                <Button
                                    icon="pi pi-pencil"
                                    size="small"
                                    as="a"
                                    :href="route('admin.kuesioner.edit', data.id)"
                                    rounded
                                    aria-label="Edit"
                                    variant="text"
                                    class="mr-2"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    variant="text"
                                    severity="danger"
                                    aria-label="Hapus"
                                    size="small"
                                    @click="deleteOne(data.id)"
                                    rounded
                                />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </DashboardLayout>
</template>
