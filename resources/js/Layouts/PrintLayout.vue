<script setup lang="ts">
import { useToast } from 'primevue/usetoast';
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { AppPageProps } from '@/types';

const page = usePage<AppPageProps>();
const toast = useToast();

const showToast = (severity: 'success' | 'warning' | 'info' | 'error', summary: string, detail: string) => {
    toast.add({ severity, summary, detail, life: 3000 });
};

onMounted(() => {
    if (page.props.flash?.success) showToast('success', 'Berhasil', page.props.flash.success);
    if (page.props.flash?.warning) showToast('warning', 'Peringatan', page.props.flash.warning);
    if (page.props.flash?.info) showToast('info', 'Info', page.props.flash.info);
    if (page.props.flash?.error) showToast('error', 'Gagal', page.props.flash.error);
});

</script>

<template>
    <Toast class="z-50" />

    <div class="min-h-screen w-full bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100">
        <!-- Konten utama -->
        <main class="max-w-4xl mx-auto p-6 print:p-0">
            <slot />
        </main>
    </div>
</template>

<style>
@media print {
    .print\\:hidden {
        display: none !important;
    }
    body {
        background: white !important;
        color: black !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
}
</style>
