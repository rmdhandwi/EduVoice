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
                    class="cursor-pointer animate-fade-in delay-300"
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
                            <p class="mt-4 text-gray-600 dark:text-gray-300">Tidak ada responden yang tersedia.</p>
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
                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 animate-fade-in delay-300">
                    <div
                        v-for="res in filteredRespondens"
                        :key="res.id"
                        :filters="filters"
                        class="group hover:border-primary-300 dark:hover:border-primary-500 cursor-pointer rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-700 dark:bg-gray-800"
                        @click="onRowClick({ data: res })"
                    >
                        <!-- Nama -->
                        <h3
                            class="group-hover:text-primary-600 dark:group-hover:text-primary-400 mb-1 text-lg font-semibold text-gray-800 dark:text-gray-100"
                        >
                            {{ res.name }}
                        </h3>

                        <!-- Judul Kuesioner -->
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ res.kuesioner_judul }}
                        </p>

                        <!-- Tanggal -->
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
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
