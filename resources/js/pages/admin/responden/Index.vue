<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AppPageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { computed, ref } from 'vue';

// Props dari backend
const page = usePage<AppPageProps>();

const respondens = computed(() => page.props.respondens);

const breadcrumb = [{ label: 'Responden' }];

const viewMode = ref<'table' | 'card'>('table');

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// 🔹 state untuk filter judul
const selectedJudul = ref<string | null>(null);

// 🔹 ambil daftar unique judul
const judulOptions = computed(() => {
    const all = respondens.value.map((r: any) => r.kuesioner_judul);
    return [...new Set(all)];
});

// 🔹 filter data berdasarkan judul + global search
const filteredRespondens = computed(() => {
    let data = respondens.value;

    // filter berdasarkan judul
    if (selectedJudul.value) {
        data = data.filter((r: any) => r.kuesioner_judul === selectedJudul.value);
    }

    return data;
});

const onRowClick = (event: any) => {
    const responden = event.data;
    router.visit(route('admin.responden.show', responden.id));
};
</script>

<template>
    <DashboardLayout :breadcrumb-items="breadcrumb" title="Daftar Responden">
        <Head title="Responden" />

        <!-- Header actions -->
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <!-- Kiri: filter judul -->
            <Select v-model="selectedJudul" :options="judulOptions" placeholder="Filter Kuesioner" showClear class="w-full sm:w-64" />

            <!-- Kanan: search + toggle -->
            <div class="flex w-full flex-1 items-center gap-3 sm:w-auto">
                <IconField class="w-full sm:w-64">
                    <InputIcon>
                        <i class="pi pi-search" />
                    </InputIcon>
                    <InputText v-model="filters['global'].value" placeholder="Cari responden..." class="w-full" />
                </IconField>

                <!-- 🔹 tombol toggle -->
                <Button
                    :icon="viewMode === 'table' ? 'pi pi-th-large' : 'pi pi-list'"
                    class="p-button-rounded p-button-outlined"
                    @click="viewMode = viewMode === 'table' ? 'card' : 'table'"
                    :aria-label="viewMode === 'table' ? 'Mode Card' : 'Mode Tabel'"
                />
            </div>
        </div>

        <!-- Konten -->
        <Card class="mb-4">
            <template #content>
                <!-- Mode Table -->
                <DataTable
                    v-if="viewMode === 'table'"
                    :value="filteredRespondens"
                    dataKey="id"
                    paginator
                    :rows="10"
                    class="animate-fade-in cursor-pointer delay-300"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    responsiveLayout="scroll"
                    scrollable
                    selectionMode="true"
                    resizableColumns
                    rowGroupMode="rowspan"
                    groupRowsBy="kuesioner_judul"
                    sortMode="single"
                    sortField="kuesioner_judul"
                    :sortOrder="1"
                    columnResizeMode="fit"
                    tableStyle="min-width: 50rem"
                    scrollHeight="600px"
                    :filters="filters"
                    @row-click="onRowClick"
                >
                    <template #empty>
                        <div class="flex flex-col items-center justify-center p-6">
                            <i class="pi pi-info-circle !text-2xl text-gray-400"></i>
                            <p class="mt-4 text-gray-600 dark:text-gray-300">Belum ada responden yang mengisi survei</p>
                        </div>
                    </template>

                    <Column header="Kuesioner" sortable field="kuesioner_judul" />
                    <Column field="name" header="Nama" sortable />
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
                </DataTable>

                <!-- Mode Card -->
                <div v-else>
                    <div v-if="filteredRespondens.length" class="animate-fade-in grid gap-6 delay-300 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="res in filteredRespondens"
                            :key="res.id"
                            class="group hover:border-primary-300 dark:hover:border-primary-500 cursor-pointer rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
                            @click="onRowClick({ data: res })"
                        >
                            <!-- Header: Avatar + Nama -->
                            <div class="flex items-center gap-4">
                                <!-- Avatar (Inisial Nama) -->
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-500 text-lg font-bold text-white shadow-md"
                                >
                                    {{ res.name?.charAt(0).toUpperCase() }}
                                </div>

                                <!-- Nama & Judul -->
                                <div>
                                    <h3
                                        class="group-hover:text-primary-600 dark:group-hover:text-primary-400 mb-0.5 text-base font-semibold text-gray-800 transition-colors dark:text-gray-100"
                                    >
                                        {{ res.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        {{ res.kuesioner_judul }}
                                    </p>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="my-4 h-px w-full bg-gray-100 dark:bg-gray-700"></div>

                            <!-- Tanggal -->
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{
                                    new Date(res.created_at).toLocaleString('id-ID', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    })
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Jika kosong -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center animate-fade-in delay-300 rounded-xl border border-dashed border-gray-300 bg-gray-50 py-16 text-center text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mb-3 h-12 w-12 text-gray-400 dark:text-gray-500"
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
                        <p class="text-sm font-medium">Belum ada responden yang mengisi survei</p>
                    </div>
                </div>
            </template>
        </Card>
    </DashboardLayout>
</template>

<style scoped>
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
