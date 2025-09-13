<script setup lang="ts">
import PrintLayout from '@/Layouts/PrintLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ArrowLeft, PrinterIcon } from 'lucide-vue-next';

const page = usePage();
const kuesioner = page.props.kuesioner;

const printPage = () => window.print();

const judul = kuesioner.judul;
</script>

<template>
    <Head :title="`Detail - ${judul}`" />
    <PrintLayout>
        <div class="rounded-xl bg-white p-8 shadow-lg dark:bg-gray-800 print:rounded-none print:shadow-none">
            <!-- Header (hanya tampil di layar, tidak saat print) -->
            <div class="mb-8 flex items-center justify-between print:hidden">
                <div class="flex gap-2">
                    <a
                        :href="route('admin.kuesioner.index')"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </a>
                    <button
                        @click="printPage"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700"
                    >
                        <PrinterIcon class="h-4 w-4" />
                        Cetak
                    </button>
                </div>
            </div>

            <!-- Info -->
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-extrabold text-gray-800 dark:text-gray-100">{{ kuesioner.judul }}</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-300">{{ kuesioner.deskripsi ?? '-' }}</p>
            </div>

            <!-- Data Responden -->
            <div class="mb-10">
                <h3 class="mb-3 text-lg font-semibold text-gray-700 dark:text-gray-200">Data Responden</h3>
                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div class="flex flex-col">
                        <label class="text-gray-500">Nama :</label>
                        <div class="border-b border-gray-400 py-1"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-gray-500">Jenis Kelamin :</label>
                        <div class="border-b border-gray-400 py-1"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-gray-500">Umur :</label>
                        <div class="border-b border-gray-400 py-1"></div>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-gray-500">Pekerjaan :</label>
                        <div class="border-b border-gray-400 py-1"></div>
                    </div>
                    <div class="col-span-2 flex flex-col">
                        <label class="text-gray-500">Pendidikan Terakhir :</label>
                        <div class="border-b border-gray-400 py-1"></div>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan -->
            <div>
                <h3 class="mb-6 text-center text-xl font-semibold text-gray-700 dark:text-gray-200">Daftar Pertanyaan</h3>
                <div v-for="(pertanyaan, idx) in kuesioner.pertanyaans" :key="pertanyaan.id" class="avoid-break mb-8">
                    <p class="mb-3 font-medium text-gray-800 dark:text-gray-100">
                        {{ idx + 1 }}. {{ pertanyaan.teks }} <span class="text-red-400">*</span>
                        <span class="ml-2 text-xs text-gray-500">
                            <template v-if="pertanyaan.tipe === 'radio'"> (Pilih salah satu) </template>
                            <template v-else-if="pertanyaan.tipe === 'checkbox'"> (Boleh lebih dari satu) </template>
                            <template v-else> ({{ pertanyaan.tipe }}) </template>
                        </span>
                    </p>

                    <!-- Radio -->
                    <div v-if="pertanyaan.tipe === 'radio'" class="avoid-break space-y-2 pl-6">
                        <label v-for="(opsi, oIdx) in pertanyaan.opsi" :key="oIdx" class="flex items-center gap-3">
                            <span class="inline-block h-4 w-4 rounded-full border border-gray-600"></span>
                            <span>{{ opsi.teks }}</span>
                        </label>
                    </div>

                    <!-- Checkbox -->
                    <div v-else-if="pertanyaan.tipe === 'checkbox'" class="space-y-2 pl-6">
                        <label v-for="(opsi, oIdx) in pertanyaan.opsi" :key="oIdx" class="flex items-center gap-3">
                            <span class="inline-block h-4 w-4 rounded border border-gray-600"></span>
                            <span>{{ opsi.teks }}</span>
                        </label>
                    </div>

                    <!-- Text -->
                    <div v-else-if="pertanyaan.tipe === 'text'" class="mt-3">
                        <div class="h-20 w-full rounded border border-gray-400"></div>
                    </div>

                    <!-- Number -->
                    <div v-else-if="pertanyaan.tipe === 'number'" class="mt-3">
                        <div class="h-8 w-32 rounded border border-gray-400"></div>
                    </div>

                    <!-- Date -->
                    <div v-else-if="pertanyaan.tipe === 'date'" class="mt-3">
                        <div class="h-8 w-48 rounded border border-gray-400"></div>
                    </div>
                </div>
            </div>

            <!-- Kritik & Saran -->
            <div class="avoid-break mt-12 grid grid-cols-2 gap-8">
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-200">Kritik</h3>
                    <div class="h-32 rounded border border-gray-400"></div>
                </div>
                <div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-200">Saran</h3>
                    <div class="h-32 rounded border border-gray-400"></div>
                </div>
            </div>
        </div>
    </PrintLayout>
</template>

<style>
@media print {
    .avoid-break {
        page-break-inside: avoid;
        break-inside: avoid;
    }
    .print\:shadow-none {
        box-shadow: none !important;
    }
    .print\:rounded-none {
        border-radius: 0 !important;
    }
}
</style>
