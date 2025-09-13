<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ClipboardList, FileText, MessageCircle, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface DashboardSummary {
    total_kuesioner: number;
    total_responden: number;
    total_kritik_saran: number;
    total_jawaban: number;
}

interface DistribusiData {
    label: string;
    value: number;
    kuesioner_id: string;
}

interface TrenResponden {
    tanggal: string;
    value: number;
    kuesioner_id: string;
}

const props = defineProps<{
    summary: DashboardSummary;
    charts: { distribusi: DistribusiData[]; tren: TrenResponden[] };
    kuesioners: { id: string; judul: string }[];
}>();

// summary
const summary = props.summary;
const distribusi = props.charts.distribusi ?? [];
const tren = props.charts.tren ?? [];

// filter kuesioner
const selectedKuesioner = ref<string | null>(null);

const kuesioner = computed(() =>
    props.kuesioners.map((k) => ({
        label: k.judul,
        value: k.id,
    })),
);

// breadcrumb
const breadcrumb = [{ label: 'Dashboard' }];

// warna chart
function generateColors(count: number) {
    const colors: string[] = [];
    const hueStep = 360 / count;
    for (let i = 0; i < count; i++) {
        colors.push(`hsl(${i * hueStep}, 70%, 50%)`);
    }
    return colors;
}

// === Distribusi Chart (pie) ===
const dataDistribusi = computed(() => {
    const data = selectedKuesioner.value ? distribusi.filter((d) => d.kuesioner_id === selectedKuesioner.value) : distribusi;

    return {
        labels: data.map((d) => d.label),
        datasets: [
            {
                data: data.map((d) => d.value),
                backgroundColor: generateColors(data.length),
            },
        ],
    };
});

const optionsDistribusi = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom', labels: { color: '#374151' } },
    },
};

// === Tren Chart (bar) ===
const chartDataTren = computed(() => {
    const data = selectedKuesioner.value ? tren.filter((d) => d.kuesioner_id === selectedKuesioner.value) : tren;

    return {
        labels: data.map((d) => d.tanggal),
        datasets: [
            {
                label: 'Jumlah Responden',
                data: data.map((d) => d.value),
                backgroundColor: ['rgba(59, 130, 246, 0.5)'],
                borderColor: ['rgb(59, 130, 246)'],
                borderWidth: 1,
            },
        ],
    };
});

const chartOptionsTren = {
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: '#374151' },
        },
    },
    scales: {
        x: {
            ticks: { color: '#6B7280' },
            grid: { color: '#E5E7EB' },
        },
        y: {
            beginAtZero: true,
            ticks: { color: '#6B7280', stepSize: 1 },
            grid: { color: '#E5E7EB' },
        },
    },
};
</script>

<template>
    <DashboardLayout title="Dashboard" :breadcrumb-items="breadcrumb">
        <Head title="Dashboard" />

        <div class="space-y-6">
            <!-- Statistik -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Kuesioner -->
                <div
                    class="animate-fade-in transform rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-500 p-6 text-white shadow-lg transition hover:scale-105"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Total Kuesioner</h3>
                        <ClipboardList class="h-8 w-8 opacity-80" />
                    </div>
                    <p class="mt-2 text-3xl font-bold">{{ summary.total_kuesioner }}</p>
                </div>

                <!-- Total Responden -->
                <div
                    class="animate-fade-in transform rounded-2xl bg-gradient-to-r from-green-500 to-emerald-500 p-6 text-white shadow-lg transition delay-100 hover:scale-105"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Total Responden</h3>
                        <Users class="h-8 w-8 opacity-80" />
                    </div>
                    <p class="mt-2 text-3xl font-bold">{{ summary.total_responden }}</p>
                </div>

                <!-- Total Jawaban -->
                <div
                    class="animate-fade-in transform rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 p-6 text-white shadow-lg transition delay-200 hover:scale-105"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Total Jawaban</h3>
                        <FileText class="h-8 w-8 opacity-80" />
                    </div>
                    <p class="mt-2 text-3xl font-bold">{{ summary.total_jawaban }}</p>
                </div>

                <!-- Total Kritik & Saran -->
                <div
                    class="animate-fade-in transform rounded-2xl bg-gradient-to-r from-yellow-500 to-orange-500 p-6 text-white shadow-lg transition delay-300 hover:scale-105"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Kritik & Saran</h3>
                        <MessageCircle class="h-8 w-8 opacity-80" />
                    </div>
                    <p class="mt-2 text-3xl font-bold">{{ summary.total_kritik_saran }}</p>
                </div>
            </div>

            <div class="space-y-6 animate-fade-in delay-300">
                <!-- Filter -->
                <div class="flex justify-center">
                    <Select
                        v-model="selectedKuesioner"
                        :options="kuesioner"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Pilih Kuesioner"
                        showClear
                        class="w-80"
                    />
                </div>

                <!-- Chart Section -->
                <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2">
                    <!-- Pie -->
                    <Card class="p-6">
                        <template #content>
                            <h2 class="mb-4 text-lg font-semibold">Distribusi Jawaban</h2>

                            <div v-if="!selectedKuesioner">
                                <p class="py-12 text-center text-gray-500">Silakan pilih kuesioner untuk melihat grafik</p>
                            </div>
                            <div v-else>
                                <Chart type="pie" :data="dataDistribusi" :options="optionsDistribusi" style="min-height: 300px" />
                            </div>
                        </template>
                    </Card>

                    <!-- Bar -->
                    <Card class="p-6">
                        <template #content>
                            <h2 class="mb-4 text-lg font-semibold">Tren Responden</h2>

                            <div v-if="!selectedKuesioner">
                                <p class="py-12 text-center text-gray-500">Silakan pilih kuesioner untuk melihat grafik</p>
                            </div>
                            <div v-else>
                                <Chart type="bar" :data="chartDataTren" :options="chartOptionsTren" style="min-height: 300px" />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
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
