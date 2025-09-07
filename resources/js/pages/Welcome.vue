<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="flex min-h-screen items-center justify-center bg-gradient-to-b from-[#fdfdfc] to-[#f6f6f4] p-4">
        <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl">
            
            <!-- Header -->
            <header class="border-b border-gray-200 p-6">
                <!-- Mobile (logo kiri & kanan di atas) -->
                <div class="flex items-center justify-center gap-6 sm:hidden">
                    <img src="img/logo_kota.png" alt="Logo Jayapura" class="h-14" />
                    <img src="img/logo_berakhlak.png" alt="Logo BerAKHLAK" class="h-12" />
                </div>
                <!-- Judul mobile -->
                <div class="mt-4 text-center sm:mt-0 sm:hidden">
                    <h1 class="text-base font-bold leading-tight text-gray-900">DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
                    <h2 class="text-sm font-semibold text-gray-700">KOTA JAYAPURA</h2>
                </div>

                <!-- Desktop (logo - judul - logo) -->
                <div class="hidden items-center justify-between sm:flex">
                    <img src="img/logo_kota.png" alt="Logo Jayapura" class="h-16" />
                    <div class="flex-1 text-center">
                        <h1 class="text-lg font-bold leading-tight text-gray-900">DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
                        <h2 class="text-md font-semibold text-gray-700">KOTA JAYAPURA</h2>
                    </div>
                    <img src="img/logo_berakhlak.png" alt="Logo BerAKHLAK" class="h-14" />
                </div>
            </header>

            <!-- Konten -->
            <main class="relative p-6 text-center space-y-4">
                <!-- Ilustrasi di belakang -->
                <img 
                    src="img/ilustrasi.png" 
                    alt="Ilustrasi" 
                    class="absolute bottom-0 right-0 w-48 opacity-10 pointer-events-none select-none"
                />

                <!-- Teks -->
                <p class="text-xl font-semibold text-gray-800 leading-relaxed tracking-wide">
                    Mari Bantu Kami untuk Meningkatkan <br class="hidden sm:block" />
                    Kualitas Pelayanan
                </p>
                <p class="text-2xl font-extrabold text-gray-900">DISDIKBUD KOTA JAYAPURA</p>
                <p class="text-base font-medium text-gray-700">Dengan mengisi</p>
                <p class="text-2xl font-bold text-gray-900 tracking-tight">Survei Kepuasan Masyarakat</p>

                <!-- QR Code + Link -->
                <div class="mt-6 flex flex-col items-center gap-3">
                    <img src="qrcode.png" alt="QR Code" class="h-40 w-40 rounded-lg shadow-md" />
                    <a
                        :href="route('login')"
                        class="rounded-full bg-gray-900 px-6 py-2 font-semibold text-white shadow transition hover:bg-gray-700"
                    >
                        SCAN DI SINI
                    </a>
                    <p class="text-sm text-gray-600">Atau kunjungi link:</p>
                    <a 
                        href="https://disdikbud.go.id/SKM" 
                        target="_blank" 
                        class="font-medium text-gray-900 underline hover:text-gray-700"
                    >
                        disdikbud.go.id/SKM
                    </a>
                </div>
            </main>
        </div>
    </div>
</template>
