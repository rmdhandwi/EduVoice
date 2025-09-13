<script setup lang="ts">
import PrintLayout from '@/Layouts/PrintLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ArrowLeft, PrinterIcon } from 'lucide-vue-next';

// Opsi jawaban untuk setiap pertanyaan
interface OpsiJawaban {
    id: string;
    teks: string;
    skor?: number | null;
    dipilih?: boolean; // true kalau jawaban ini dipilih responden
}

// Interface sesuai backend
interface Pertanyaan {
    id: string;
    teks?: string;
    tipe: 'radio' | 'checkbox' | 'text' | 'number' | 'date';
    opsi_id?: string | null; // radio
    opsi_terpilih?: string[] | null; // checkbox
    teks_jawaban?: string | null;
    angka?: number | null;
    tanggal?: string | null;
    opsi: OpsiJawaban[];
}

interface Kuesioner {
    id: string;
    judul: string;
    deskripsi: string;
    pertanyaans: Pertanyaan[];
}

interface Responden {
    id: string;
    name: string;
    jk: string;
    umur: number;
    pekerjaan: string;
    pendidikan_terakhir: string;
    kritik?: string | null;
    saran?: string | null;
    kuesioners?: Kuesioner[];
}

const props = defineProps<{ responden: Responden; pertanyaan: Pertanyaan[] }>();

const printPage = () => window.print();
</script>

<template>
    <Head title="Detail Jawaban Responden" />
    <PrintLayout>
        <div class="print rounded-xl bg-white p-6 shadow dark:bg-gray-800">
            <!-- Header -->
            <div class="mb-6 flex items-center  justify-between sticky print:hidden">
                <div class="flex gap-2">
                    <a
                        :href="route('admin.responden.index')"
                        class="inline-flex items-center gap-1 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        Kembali
                    </a>
                    <button
                        @click="printPage"
                        class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        <PrinterIcon class="h-4 w-4" />
                        Cetak
                    </button>
                </div>
            </div>

            <!-- Judul Kuesioner -->
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold">{{ props.responden.kuesioners?.[0]?.judul ?? '-' }}</h2>
                <p class="text-gray-500 dark:text-gray-400">{{ props.responden.kuesioners?.[0]?.deskripsi ?? '-' }}</p>
            </div>

            <!-- Data Responden -->
            <div class="mb-6">
                <h3 class="mb-2 border-t border-b border-gray-200 p-2 text-lg font-semibold dark:border-gray-600">Data Responden</h3>
                <table class="w-full overflow-hidden rounded-lg border border-gray-200 text-sm dark:border-gray-700">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="w-1/3 px-3 py-2 font-bold text-gray-700 dark:text-gray-300">Nama</td>
                            <td class="px-3 py-2">{{ props.responden.name }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 font-bold text-gray-700 dark:text-gray-300">Jenis Kelamin</td>
                            <td class="px-3 py-2">{{ props.responden.jk }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 font-bold text-gray-700 dark:text-gray-300">Umur</td>
                            <td class="px-3 py-2">{{ props.responden.umur }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-3 py-2 font-bold text-gray-700 dark:text-gray-300">Pekerjaan</td>
                            <td class="px-3 py-2">{{ props.responden.pekerjaan }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-bold text-gray-700 dark:text-gray-300">Pendidikan Terakhir</td>
                            <td class="px-3 py-2">{{ props.responden.pendidikan_terakhir }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Jawaban Pertanyaan -->
            <div>
                <h3 class="mb-2 border-t border-b border-gray-200 p-2 text-lg font-semibold dark:border-gray-600">Jawaban Pertanyaan</h3>
                <div
                    v-for="(pertanyaan, idx) in props.responden.kuesioners?.[0]?.pertanyaans ?? []"
                    :key="pertanyaan.id"
                    class="mb-4 border-b border-gray-200 pb-3 dark:border-gray-600 sssssssssssss avoid-break"
                >
                    <p class="mb-1 font-medium">{{ idx + 1 }}. {{ pertanyaan.teks ?? '-' }}</p>

                    <!-- Radio -->
                    <div v-if="pertanyaan.tipe === 'radio'" class="flex flex-col gap-2">
                        <div
                            v-for="opsi in pertanyaan.opsi"
                            :key="opsi.id"
                            class="flex items-center gap-2 rounded-lg border p-2"
                            :class="{
                                'border-blue-400 bg-blue-50': opsi.dipilih,
                                'border-gray-300 bg-gray-50': !opsi.dipilih,
                                'pointer-events-none': true,
                            }"
                        >
                            <input type="radio" :checked="opsi.dipilih" class="h-4 w-4 accent-blue-600" />
                            <span class="text-gray-700 dark:text-gray-700">{{ opsi.teks }}</span>
                        </div>
                    </div>

                    <!-- Checkbox -->
                    <div v-else-if="pertanyaan.tipe === 'checkbox'" class="flex flex-col gap-2">
                        <div
                            v-for="opsi in pertanyaan.opsi"
                            :key="opsi.id"
                            class="flex items-center gap-2 rounded-lg border p-2"
                            :class="{
                                'border-green-400 bg-green-50': opsi.dipilih,
                                'border-gray-300 bg-gray-50': !opsi.dipilih,
                                'pointer-events-none': true,
                            }"
                        >
                            <input type="checkbox" :checked="opsi.dipilih" class="h-4 w-4 accent-green-600" />
                            <span class="text-gray-700 dark:text-gray-300">{{ opsi.teks }}</span>
                        </div>
                    </div>

                    <!-- Text -->
                    <div v-else-if="pertanyaan.tipe === 'text'">
                        <p class="text-gray-700 italic dark:text-gray-300">
                            {{ pertanyaan.teks_jawaban ?? '-' }}
                        </p>
                    </div>

                    <!-- Number -->
                    <div v-else-if="pertanyaan.tipe === 'number'">
                        <p class="text-gray-700 italic dark:text-gray-300">
                            {{ pertanyaan.angka ?? '-' }}
                        </p>
                    </div>

                    <!-- Date -->
                    <div v-else-if="pertanyaan.tipe === 'date'">
                        <p class="text-gray-700 italic dark:text-gray-300">
                            {{
                                new Date(pertanyaan.tanggal ?? '-').toLocaleString('id-ID', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                })
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kritik & Saran -->
            <div class="avoid-break mt-6 grid grid-cols-1 gap-6">
                <div>
                    <h3 class="mb-1 text-lg font-semibold">Kritik</h3>
                    <Textarea rows="10" v-model="props.responden.kritik" class="w-full" disabled style="resize: none" />
                </div>
                <div>
                    <h3 class="mb-1 text-lg font-semibold">Saran</h3>
                    <Textarea rows="10" v-model="props.responden.saran" class="w-full" disabled style="resize: none" />
                </div>
            </div>
        </div>
    </PrintLayout>
</template>

<style>
@media print {
    /* Jangan potong pertanyaan di tengah halaman */
    .avoid-break {
        page-break-inside: avoid;
        break-inside: avoid; /* Untuk browser modern */
    }

    /* Hilangkan shadow & border bulat saat print */
    .print\\:shadow-none {
        box-shadow: none !important;
    }
    .print\\:rounded-none {
        border-radius: 0 !important;
    }
}
</style>
