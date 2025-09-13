<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Award, BarChart3, Calculator, Printer, Users } from 'lucide-vue-next'; // ✅ Ikon
import { nextTick, ref } from 'vue';

const props = defineProps<{
    kuesioner: {
        id: string;
        judul: string;
        deskripsi?: string;
    };
    unsurs: {
        unsur: string;
        nilai_unsur: number;
        nilai_tertimbang: number;
    }[];
    ikm: number;
    skm: number;
    nilai_dasar: number;
    total_tertimbang: number;
    mutu: string;
    jumlah_responden: number;
}>();

function getLabel(mutu: string) {
    if (mutu.includes('A')) return 'Sangat Baik';
    if (mutu.includes('B')) return 'Baik';
    if (mutu.includes('C')) return 'Kurang Baik';
    return 'Tidak Baik';
}

const breadcrumb = [{ label: 'Laporan', route: 'laporan.index' }, { label: `${props.kuesioner.judul}` }, { label: 'Detail' }];

const windowPrinting = ref(false);

function printPage() {
    windowPrinting.value = true; // tampilkan layout print
    nextTick(() => {
        window.print();
        setTimeout(() => {
            windowPrinting.value = false; // balik ke layout normal
        }, 500);
    });
}
</script>

<template>
    <DashboardLayout :breadcrumb-items="breadcrumb" :title="`Detail ${props.kuesioner.judul}`" v-if="!windowPrinting">
        <Head :title="`Detail IKM - ${kuesioner.judul}`" />

        <div class="space-y-10 p-6">
            <!-- Header -->
            <div class="text-center">
                <div
                    class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 text-white shadow-lg"
                >
                    <BarChart3 class="h-8 w-8" />
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">Detail Kuesioner</h1>
                <button @click="printPage" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-700">
                    <Printer class="h-5 w-5" /> Print
                </button>
                <p class="mt-1 text-lg font-semibold text-gray-600 dark:text-gray-300">
                    {{ kuesioner.judul }}
                </p>
                <p v-if="kuesioner.deskripsi" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ kuesioner.deskripsi }}
                </p>
            </div>

            <!-- Tabel Unsur -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full border-collapse text-sm">
                    <thead class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white">
                        <tr>
                            <th class="px-4 py-2 text-center">No</th>
                            <th class="px-4 py-2 text-left">Unsur Pelayanan</th>
                            <th class="px-4 py-2 text-center">Nilai Unsur</th>
                            <th class="px-4 py-2 text-center">Nilai IKM Tertimbang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(u, i) in unsurs"
                            :key="i"
                            :class="i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700/40' : 'bg-white dark:bg-gray-800'"
                            class="transition hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                        >
                            <td class="px-4 py-2 text-center text-gray-700 dark:text-gray-300">{{ i + 1 }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ u.unsur }}</td>
                            <td class="px-4 py-2 text-center font-medium text-gray-800 dark:text-gray-200">{{ u.nilai_unsur }}</td>
                            <td class="px-4 py-2 text-center font-semibold text-blue-600 dark:text-blue-400">{{ u.nilai_tertimbang }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan -->
            <div class="grid gap-6 md:grid-cols-4">
                <div
                    class="flex flex-col items-center rounded-xl border border-gray-200 bg-gradient-to-br from-green-50 to-green-100 p-6 shadow-md dark:from-green-900 dark:to-green-800"
                >
                    <Users class="h-8 w-8 text-green-600 dark:text-green-300" />
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Jumlah Responden</p>
                    <p class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ props.jumlah_responden }}</p>
                </div>

                <div
                    v-for="item in [
                        { label: 'Rata-Rata IKM', value: ikm, icon: BarChart3, color: 'blue' },
                        { label: 'Nilai Dasar', value: nilai_dasar, icon: Calculator, color: 'indigo' },
                        { label: 'Hasil (Total x Nilai Dasar) SKM', value: skm, icon: Award, color: 'yellow' },
                    ]"
                    :key="item.label"
                    class="flex flex-col items-center rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50 to-indigo-100 p-6 shadow-md dark:from-gray-800 dark:to-gray-700"
                >
                    <component :is="item.icon" class="h-8 w-8 text-indigo-600 dark:text-indigo-300" />
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ item.label }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ item.value }}</p>
                </div>
            </div>

            <!-- Mutu -->
            <div class="flex flex-col items-center justify-center p-8 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">Mutu Pelayanan</p>
                <div
                    class="mt-4 flex h-24 w-24 items-center justify-center rounded-full text-5xl font-extrabold shadow-md"
                    :class="{
                        'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300': mutu.includes('A'),
                        'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300': mutu.includes('B'),
                        'bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': mutu.includes('C'),
                        'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300': mutu.includes('D'),
                    }"
                >
                    {{ mutu.charAt(0) }}
                </div>
                <p class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-300">{{ getLabel(mutu) }}</p>
            </div>
        </div>
    </DashboardLayout>

    <!-- Print mode -->
    <div v-else class="p-6 text-gray-900">
        <h1 class="mb-2 text-center text-xl font-bold">Laporan IKM</h1>
        <h2 class="mb-6 text-center text-lg font-semibold">{{ kuesioner.judul }}</h2>

        <table class="w-full border border-gray-400 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">No</th>
                    <th class="border px-2 py-1">Unsur Pelayanan</th>
                    <th class="border px-2 py-1">Nilai Unsur</th>
                    <th class="border px-2 py-1">Nilai Tertimbang</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(u, i) in unsurs" :key="i">
                    <td class="border px-2 py-1 text-center">{{ i + 1 }}</td>
                    <td class="border px-2 py-1">{{ u.unsur }}</td>
                    <td class="border px-2 py-1 text-center">{{ u.nilai_unsur }}</td>
                    <td class="border px-2 py-1 text-center">{{ u.nilai_tertimbang }}</td>
                </tr>
            </tbody>
        </table>

        <table class="mt-6 w-full border border-gray-400 text-sm">
            <tbody>
                <tr>
                    <td colspan="3" class="border px-2 py-1 font-semibold">Jumlah Responden</td>
                    <td class="border px-2 py-1 text-center">{{ jumlah_responden }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="border px-2 py-1 font-semibold">IKM</td>
                    <td class="border px-2 py-1 text-center">{{ ikm }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="border px-2 py-1 font-semibold">Nilai Dasar</td>
                    <td class="border px-2 py-1 text-center">{{ nilai_dasar }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="border px-2 py-1 font-semibold">SKM</td>
                    <td class="border px-2 py-1 text-center">{{ skm }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="border text-center px-2 py-1 font-semibold">Mutu {{ getLabel(mutu) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    table {
        border-collapse: collapse;
    }
}
</style>
