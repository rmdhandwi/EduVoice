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

// Ambil data kuesioner & pertanyaan
const kuesioner = computed(() => page.props.kuesioner); // detail kuesioner
const pertanyaans = computed(() => page.props.pertanyaans); // daftar pertanyaan

// State untuk DataTable
const selectedPertanyaans = ref<any[]>([]);

const breadcrumb = [{ label: 'Kuesioner', route: 'admin.kuesioner.index' }, { label: kuesioner.value.judul }];

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const editSelected = () => {
    if (!selectedPertanyaans.value.length) return;
    const ids = selectedPertanyaans.value.map((p) => p.id);

    router.visit(
        route('admin.kuesioner.pertanyaan.bulkEdit', {
            kuesioner: kuesioner.value.id,
            ids, // kirim via query
        }),
        {
            preserveScroll: true,
        },
    );
};

// Hapus satu
const deleteOne = (id: string) => {
    confirm.require({
        group: 'deleteConfirm',
        header: 'Konfirmasi Hapus',
        message: 'Apakah kamu yakin ingin menghapus pertanyaan ini?',
        accept: () => {
            router.delete(
                route('admin.kuesioner.pertanyaan.destroy', {
                    kuesioner: kuesioner.value.id,
                    pertanyaan: id,
                }),
                {
                    preserveScroll: true,
                },
            );
        },
    });
};

// Hapus banyak
const deleteSelected = () => {
    if (!selectedPertanyaans.value.length) return;

    confirm.require({
        group: 'deleteConfirm',
        header: 'Konfirmasi Hapus Banyak',
        message: `Apakah kamu yakin ingin menghapus ${selectedPertanyaans.value.length} pertanyaan terpilih?`,
        accept: () => {
            const ids = selectedPertanyaans.value.map((p) => p.id);

            router.post(
                route('admin.kuesioner.pertanyaan.bulkDestroy', {
                    kuesioner: kuesioner.value.id,
                }),
                { ids },
                {
                    preserveScroll: true,
                    onSuccess: () => (selectedPertanyaans.value = []),
                },
            );
        },
    });
};

// Klik baris → edit
const onRowClick = (event: any) => {
    const pertanyaan = event.data;

    router.visit(
        route('admin.kuesioner.pertanyaan.edit', {
            kuesioner: page.props.kuesioner.id, // param pertama
            pertanyaan: pertanyaan.id, // param kedua
        }),
    );
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

    <DashboardLayout :breadcrumb-items="breadcrumb" :title="`Pertanyaan - ${kuesioner.judul}`">
        <Head :title="`Pertanyaan - ${kuesioner.judul}`" />

        <!-- Header actions -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Button
                label="Tambah Pertanyaan"
                as="a"
                :href="route('admin.kuesioner.pertanyaan.create', kuesioner.id)"
                icon="pi pi-plus"
                size="small"
            />

            <div class="flex w-full sm:w-64">
                <IconField class="w-full">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Cari pertanyaan..." class="w-full" />
                </IconField>
            </div>
        </div>

        <!-- Bulk actions -->
        <div v-if="selectedPertanyaans.length > 0" class="mb-4 flex items-center justify-between rounded-lg bg-blue-50 p-3 text-blue-700">
            <span>{{ selectedPertanyaans.length }} pertanyaan dipilih</span>
            <div class="flex gap-2">
                <Button icon="pi pi-pencil" label="Edit Terpilih" size="small" severity="info" @click="editSelected" />
                <Button icon="pi pi-trash" label="Hapus Terpilih" size="small" severity="danger" @click="deleteSelected" />
            </div>
        </div>

        <!-- Tabel pertanyaan -->
        <Card class="mb-4">
            <template #content>
                <DataTable
                    :value="pertanyaans"
                    v-model:selection="selectedPertanyaans"
                    dataKey="id"
                    selectionMode="checkbox"
                    paginator
                    :rows="10"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    responsiveLayout="scroll"
                    scrollable
                    resizableColumns
                    columnResizeMode="fit"
                    tableStyle="min-width: 50rem"
                    scrollHeight="580px"
                    :filters="filters"
                    @row-click="onRowClick"
                    sortMode="single"
                    sortField="unsur"
                    groupRowsBy="unsur"
                    :sortOrder="1"
                    rowGroupMode="rowspan"
                >
                    <template #empty>
                        <div class="flex flex-col items-center justify-center p-6">
                            <i class="pi pi-info-circle !text-2xl text-gray-400"></i>
                            <p class="mt-4 text-gray-600 dark:text-gray-300">Tidak ada pertanyaan yang tersedia.</p>
                        </div>
                    </template>

                    <!-- Checkbox -->
                    <Column selectionMode="multiple" headerStyle="width: 3rem" />

                    <!-- Tipe -->
                    <Column field="unsur" header="Unsur" sortable />

                    <!-- Teks pertanyaan -->
                    <Column field="teks" header="Pertanyaan" sortable />

                    <!-- Dibuat -->
                    <Column field="created_at" header="Dibuat" sortable>
                        <template #body="{ data }">
                            {{
                                new Date(data.created_at).toLocaleString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                })
                            }}
                        </template>
                    </Column>

                    <!-- Action -->
                    <Column header="Aksi" style="width: 100px">
                        <template #body="{ data }">
                            <Button
                                icon="pi pi-trash"
                                variant="text"
                                severity="danger"
                                aria-label="Hapus"
                                size="small"
                                @click.stop="deleteOne(data.id)"
                                rounded
                            />
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </DashboardLayout>
</template>
