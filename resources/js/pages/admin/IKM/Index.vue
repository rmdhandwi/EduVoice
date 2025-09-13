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

const props = defineProps<{ ikms: IKM[] }>();
const ikms = props.ikms;

const breadcrumb = [{ label: 'IKM' }];

function getLabel(mutu: string) {
    if (mutu.includes('A')) return 'Sangat Baik';
    if (mutu.includes('B')) return 'Baik';
    if (mutu.includes('C')) return 'Kurang Baik';
    return 'Tidak Baik';
}
</script>

<template>
    <DashboardLayout :breadcrumb-items="breadcrumb" title="Index Kepuasan Masyarakat">
        <Head title="IKM" />

        <div class="space-y-6 p-6">
            <!-- Grid Kartu -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <template v-for="(ikm, i) in ikms" :key="ikm.id">
                    <Button
                        as="a"
                        unstyled
                        :href="route('admin.ikm.show', ikm.id)"
                        class="group animate-fade-in relative overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-white to-gray-50 p-6 shadow-lg transition-all duration-500 hover:translate-y-[-4px] hover:scale-[102%] hover:shadow-2xl dark:border-gray-700 dark:from-gray-800 dark:to-gray-900"
                        :class="[`delay-${(i + 1) * 100}`]"
                    >
                        <!-- Overlay Blur -->
                        <div
                            class="absolute inset-0 z-10 flex items-center justify-center rounded-2xl bg-black/30 opacity-0 backdrop-blur-sm transition-opacity duration-500 group-hover:opacity-100"
                        >
                            <span class="text-lg font-semibold text-white">Lihat Detail</span>
                        </div>

                        <!-- Isi Card -->
                        <div class="relative z-0">
                            <!-- Judul -->
                            <h2 class="mb-4 text-xl font-bold text-gray-800 dark:text-gray-100">
                                {{ ikm.judul }}
                            </h2>

                            <!-- Konten -->
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Kolom Kiri -->
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai IKM</p>
                                        <p class="text-2xl font-extrabold text-blue-700 dark:text-blue-400">
                                            {{ ikm.ikm }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Nilai SKM</p>
                                        <p class="text-2xl font-extrabold text-green-700 dark:text-green-400">
                                            {{ ikm.skm }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Responden</p>
                                        <p class="text-2xl font-extrabold text-rose-700 dark:text-rose-400">
                                            {{ ikm.jumlah_responden }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="flex flex-col items-center justify-center text-center">
                                    <div
                                        class="flex h-16 w-16 items-center justify-center rounded-full text-3xl font-extrabold shadow-md transition-transform duration-500 group-hover:rotate-6"
                                        :class="{
                                            'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': ikm.mutu.includes('A'),
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300': ikm.mutu.includes('B'),
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': ikm.mutu.includes('C'),
                                            'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300': ikm.mutu.includes('D'),
                                        }"
                                    >
                                        {{ ikm.mutu.charAt(0) }}
                                    </div>
                                    <p class="mt-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ getLabel(ikm.mutu) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </Button>
                </template>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.7s ease-out both;
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
.delay-400 {
    animation-delay: 0.4s;
}
.delay-500 {
    animation-delay: 0.5s;
}
</style>
