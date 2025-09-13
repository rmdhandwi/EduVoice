<script setup lang="ts">
import Header from '@/Components/Header.vue';
import Sidebar from '@/Components/Sidebar.vue';
import { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronRight, HomeIcon } from 'lucide-vue-next';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, watch } from 'vue';

// ambil data dari inertia
const page = usePage<AppPageProps>();
const role = page.props.auth.user.role;

// state sidebar
const sidebarOpen = ref(true);
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value);

// toast notifikasi
const toast = useToast();
const showToast = (severity: 'success' | 'warning' | 'info' | 'error', summary: string, detail: string) => {
    toast.add({ severity, summary, detail, life: 3000 });
};

// flash message dari laravel
onMounted(() => {
    if (page.props.flash?.success) showToast('success', 'Berhasil', page.props.flash.success);
    if (page.props.flash?.warning) showToast('warning', 'Peringatan', page.props.flash.warning);
    if (page.props.flash?.info) showToast('info', 'Info', page.props.flash.info);
    if (page.props.flash?.error) showToast('error', 'Gagal', page.props.flash.error);
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) showToast('success', 'Berhasil', flash.success);
        if (flash?.error) showToast('error', 'Gagal', flash.error);
        if (flash?.warning) showToast('warning', 'Peringatan', flash.warning);
        if (flash?.info) showToast('info', 'Info', flash.info);
    },
    { deep: true },
);

// props untuk breadcrumb + title
interface BreadcrumbItem {
    label: string;
    route?: string;
    global?: boolean;
}

const props = defineProps<{
    title?: string;
    breadcrumbItems?: BreadcrumbItem[];
}>();

// mapping prefix route berdasarkan role
const prefix = role === 'admin' ? 'admin.' : role === 'kepala' ? 'kepala.' : '';

// helper resolve route
const resolveRoute = (name: string, global: boolean = false) => {
    if (global) return route(name); // route global tanpa prefix
    if (name.startsWith('admin.') || name.startsWith('kepala.')) return route(name);
    return route(prefix + name);
};

// default home dashboard
const home = {
    icon: HomeIcon,
    route: prefix + 'dashboard',
};
</script>

<template>
    <Toast class="z-[9999]" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Sidebar -->
        <Sidebar :is-open="sidebarOpen" @toggle="toggleSidebar" />

        <!-- Main -->
        <div
            :class="[
                'flex flex-1 flex-col transition-all duration-300',
                // hanya kasih margin kiri di desktop
                sidebarOpen ? 'md:ml-64' : 'md:ml-20',
                'ml-0',
            ]"
        >
            <!-- Header -->
            <Header @toggle="toggleSidebar" />

            <!-- Breadcrumb + Title -->
            <div class="flex flex-col gap-2 border-b border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                <!-- Judul Halaman -->
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ props.title }}
                </h1>

                <!-- Breadcrumb -->
                <nav class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                    <!-- Home -->
                    <a :href="resolveRoute(home.route)" class="flex items-center gap-1 text-blue-600 hover:underline dark:text-blue-400">
                        <component :is="home.icon" class="h-4 w-4" />
                    </a>

                    <!-- Loop Items -->
                    <template v-for="(item, idx) in props.breadcrumbItems" :key="idx">
                        <ChevronRight class="mx-2 h-4 w-4 text-gray-400" />

                        <a v-if="item.route" :href="resolveRoute(item.route, item.global)" class="text-blue-600 hover:underline dark:text-blue-400">
                            {{ item.label }}
                        </a>
                        <span v-else class="text-gray-700 dark:text-gray-200">
                            {{ item.label }}
                        </span>
                    </template>
                </nav>
            </div>

            <!-- Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

