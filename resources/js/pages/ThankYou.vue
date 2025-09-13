<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Terima Kasih!" />
    <div
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 p-6"
    >
        <!-- Animated Background Text -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                v-for="n in 15"
                :key="n"
                class="animate-float absolute text-6xl font-extrabold whitespace-nowrap text-white/10"
                :style="{
                    top: `${Math.random() * 100}%`,
                    left: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 10}s`,
                    animationDuration: `${10 + Math.random() * 10}s`,
                }"
            >
                TERIMA KASIH!
            </div>
        </div>

        <!-- Card Content -->
        <div class="animate-fadeIn relative z-10 w-full max-w-md rounded-2xl bg-white p-10 text-center shadow-2xl dark:bg-gray-900">
            <!-- Icon -->
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600">
                <i class="pi pi-check text-4xl"></i>
            </div>

            <!-- Title -->
            <h1 class="mb-2 text-2xl font-bold text-gray-800 dark:text-gray-100">Terima Kasih!</h1>
            <p class="mb-8 text-gray-600 dark:text-gray-400">
                Kami sangat menghargai partisipasi Anda dalam mengisi kuesioner ini. Masukan Anda akan sangat membantu kami dalam meningkatkan
                layanan.
            </p>

            <!-- Button -->
            <Button
                unstyled
                as="a"
                :href="route('user.kuesioner.index')"
                class="w-full rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-3 font-semibold text-white shadow-lg transition hover:opacity-90"
            >
                Kembali ke Halaman
            </Button>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fadeIn {
    animation: fadeIn 0.5s ease-out;
}

@keyframes float {
    0% {
        transform: translateY(0) translateX(0) rotate(0deg);
    }
    50% {
        transform: translateY(-50px) translateX(30px) rotate(10deg);
    }
    100% {
        transform: translateY(0) translateX(0) rotate(0deg);
    }
}
.animate-float {
    animation-name: float;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}
</style>
