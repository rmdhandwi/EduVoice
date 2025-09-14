<script setup>
defineProps({
    status: Number,
    message: String,
});
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-gray-100 text-center dark:bg-gray-900">
        <h1 class="text-7xl font-extrabold text-red-600">{{ status }}</h1>
        <p class="mt-4 text-lg text-gray-700 dark:text-gray-300">
            {{ message }}
        </p>
        <a :href="route('home')" class="mt-6 rounded-lg bg-indigo-600 px-6 py-2 font-semibold text-white transition hover:bg-indigo-700"> Kembali ke Beranda </a>
    </div>
</template>
