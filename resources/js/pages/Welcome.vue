<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div
        class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 p-4 dark:from-gray-950 dark:via-gray-900 dark:to-gray-800"
    >
        <div
            class="relative w-full max-w-3xl overflow-hidden rounded-3xl border border-white/30 bg-white/60 shadow-2xl backdrop-blur-xl transition-colors duration-300 dark:border-gray-700 dark:bg-gray-900/70"
        >
            <!-- Header -->
            <header class="border-b border-gray-200 p-6 dark:border-gray-700">
                <!-- Mobile -->
                <div class="flex flex-col items-center gap-4 sm:hidden">
                    <div class="flex gap-6">
                        <img src="img/logo_kota.png" alt="Logo Jayapura" class="h-14" />
                        <img src="img/logo_berakhlak.png" alt="Logo BerAKHLAK" class="h-12" />
                    </div>
                    <div class="text-center">
                        <h1 class="text-base leading-tight font-bold text-gray-900 dark:text-gray-100">DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
                        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300">KOTA JAYAPURA</h2>
                    </div>
                </div>

                <!-- Desktop -->
                <div class="hidden items-center justify-between sm:flex">
                    <img src="img/logo_kota.png" alt="Logo Jayapura" class="h-16" />
                    <div class="flex-1 text-center">
                        <h1 class="text-lg leading-tight font-bold text-gray-900 dark:text-gray-100">DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
                        <h2 class="text-md font-semibold text-gray-700 dark:text-gray-300">KOTA JAYAPURA</h2>
                    </div>
                    <img src="img/logo_berakhlak.png" alt="Logo BerAKHLAK" class="h-14" />
                </div>
            </header>

            <!-- Konten -->
            <main class="relative flex flex-col items-center space-y-6 p-10 text-center">
                <!-- Ilustrasi dekoratif -->
                <img src="img/ilustrasi.png" alt="Ilustrasi" class="pointer-events-none absolute -right-10 -bottom-10 w-56 opacity-10 select-none" />

                <!-- Teks -->
                <p class="text-xl leading-relaxed font-semibold tracking-wide text-gray-800 dark:text-gray-200">
                    Mari Bantu Kami untuk Meningkatkan
                    <br class="hidden sm:block" />
                    Kualitas Pelayanan
                </p>
                <p class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">DISDIKBUD KOTA JAYAPURA</p>
                <p class="text-base font-medium text-gray-700 dark:text-gray-300">Dengan Mengisi</p>
                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Survei Kepuasan Masyarakat</p>

                <!-- Tombol masuk -->
                <div class="mt-6 flex flex-col items-center gap-4">
                    <a
                        :href="route('user.kuesioner.index')"
                        class="rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-3 text-lg font-semibold text-white shadow-lg transition-all duration-300 hover:scale-[1.05] hover:from-blue-700 hover:to-purple-700 active:scale-95"
                    >
                        Mulai Survei
                    </a>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Klik tombol di atas untuk melanjutkan</p>
                </div>
            </main>
        </div>
    </div>
</template>
