<script lang="ts" setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';

const breadcrumb = [{ label: 'Dashboard' }];
</script>

<template>
    <DashboardLayout title="Dashboard" :breadcrumb-items="breadcrumb">
        <Head title="Dashboard" />
        <div>
            <h2 class="text-dark mb-4 text-xl font-bold dark:text-gray-100">Selamat Datang!</h2>
            <p class="text-dark dark:text-gray-100">Ini adalah halaman utama dashboard.</p>
        </div>
    </DashboardLayout>
</template>
