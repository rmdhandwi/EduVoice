<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Award, BarChart3, Calculator, Users } from 'lucide-vue-next'; // ✅ Ikon

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

const breadcrumb = [{ label: 'IKM', route: 'ikm.index' }, { label: `${props.kuesioner.judul}` }];
</script>

<template>
    <DashboardLayout :breadcrumb-items="breadcrumb" :title="`Detail ${props.kuesioner.judul}`">
        <Head :title="`Detail IKM - ${kuesioner.judul}`" />

        <div class="space-y-10 p-6">
            <!-- Header -->
            <div class="text-center">
                <div
                    class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 text-white shadow-lg"
                >
                    <BarChart3 class="h-8 w-8" />
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">Hasil Kuesioner</h1>
                <p class="mt-1 text-lg font-semibold text-gray-600 dark:text-gray-300">
                    {{ kuesioner.judul }}
                </p>
                <p v-if="kuesioner.deskripsi" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ kuesioner.deskripsi }}
                </p>
            </div>

            <!-- Tabel Unsur -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <table v-if="unsurs.length" class="w-full border-collapse text-sm">
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

                <!-- Empty State -->
                <div v-else class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                        <svg
                            class="h-6 w-6 text-gray-400 dark:text-gray-300"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 17v-2h6v2m-7-4h8m-9-4h10M5 7h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z"
                            />
                        </svg>
                    </div>
                    <p class="font-semibold">Belum ada data unsur pelayanan</p>
                </div>
            </div>

            <!-- Ringkasan -->
            <div v-if="props.jumlah_responden > 0" class="grid gap-6 md:grid-cols-4">
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
            <div v-if="mutu" class="flex flex-col items-center justify-center p-8 text-center">
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

            <div v-else class="p-8 text-center text-gray-500 dark:text-gray-400">
                <p class="font-semibold">Belum ada data mutu</p>
            </div>
        </div>
    </DashboardLayout>
</template>
