<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

interface IKM {
    id: number;
    judul: string;
    ikm: number;
    skm: number;
    mutu: string;
    jumlah_responden: number;
}

defineProps<{ laporan: IKM[] }>();

const breadcrumb = [{ label: 'Laporan' }];

function getLabel(mutu: string) {
    if (mutu.includes('A')) return 'Sangat Baik';
    if (mutu.includes('B')) return 'Baik';
    if (mutu.includes('C')) return 'Kurang Baik';
    return 'Tidak Baik';
}
</script>

<template>
    <DashboardLayout :breadcrumb-items="breadcrumb">
        <Head title="Laporan IKM" />

        <div class="space-y-8 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">Laporan Indeks Kepuasan Masyarakat</h1>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Rekap hasil seluruh kuesioner yang sudah dikumpulkan</p>
                </div>
                <div class="flex gap-3">
                    <Button as="a" href="#" class="rounded-lg bg-green-600 px-4 py-2 font-semibold text-white shadow hover:bg-green-700">
                        Export Excel
                    </Button>
                    <Button as="a" href="#" class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white shadow hover:bg-red-700">
                        Export PDF
                    </Button>
                </div>
            </div>

            <!-- Tabel Rekap -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-md dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Judul Kuesioner</th>
                            <th class="px-4 py-3 text-center">IKM</th>
                            <th class="px-4 py-3 text-center">SKM</th>
                            <th class="px-4 py-3 text-center">Mutu</th>
                            <th class="px-4 py-3 text-center">Responden</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, i) in laporan" :key="row.id" class="border-t hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                            <td class="px-4 py-3">{{ i + 1 }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-gray-100">{{ row.judul }}</td>
                            <td class="px-4 py-3 text-center font-bold text-blue-600 dark:text-blue-400">{{ row.ikm }}</td>
                            <td class="px-4 py-3 text-center font-bold text-green-600 dark:text-green-400">{{ row.skm }}</td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': row.mutu.includes('A'),
                                        'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300': row.mutu.includes('B'),
                                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': row.mutu.includes('C'),
                                        'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': row.mutu.includes('D'),
                                    }"
                                >
                                    {{ getLabel(row.mutu) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-red-600 dark:text-red-400">{{ row.jumlah_responden }}</td>
                            <td class="px-4 py-3 text-center">
                                <Button
                                    as="a"
                                    :href="route('kepala.laporan.show', row.id)"
                                    class="rounded-lg bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow hover:bg-blue-700"
                                >
                                    Detail
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>
</template>
