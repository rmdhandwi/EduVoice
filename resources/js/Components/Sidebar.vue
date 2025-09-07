<script setup lang="ts">
import type { AppPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { FileText, Home, Menu, Power, Settings } from 'lucide-vue-next';
import { useConfirm, useToast } from 'primevue';
import { defineEmits, defineProps } from 'vue';

// props & emit
const props = defineProps<{ isOpen: boolean }>();
const emit = defineEmits<{ (e: 'toggle'): void }>();

const page = usePage<AppPageProps>();
const role = page.props.auth.user.role;

// cek route aktif
const isActiveStartsWith = (prefix: string) => {
    return route().current(`${prefix}*`);
};


const confirm = useConfirm();
const toast = useToast();

const confirmLogout = () => {
    confirm.require({
        group: 'logoutConfirm',
        header: 'Konfirmasi Logout',
        message: 'Apakah kamu yakin ingin keluar?',
        accept: () => {
            router.post(route('auth.logout'));
        },
        reject: () => {
            toast.add({
                severity: 'info',
                summary: 'Dibatalkan',
                detail: 'Logout dibatalkan',
                life: 3000,
            });
        },
    });
};
</script>

<template>
    <Toast class="z-99" />
    <ConfirmDialog group="logoutConfirm">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="bg-surface-0 dark:bg-surface-900 flex flex-col items-center rounded p-8">
                <div class="-mt-20 inline-flex h-24 w-24 items-center justify-center rounded-full bg-red-500 text-white">
                    <i class="pi pi-power-off !text-4xl"></i>
                </div>
                <span class="mt-6 mb-2 block text-2xl font-bold">{{ message.header }}</span>
                <p class="mb-0">{{ message.message }}</p>
                <div class="mt-6 flex items-center gap-2">
                    <Button label="Ya, Logout" severity="danger" @click="acceptCallback" />
                    <Button label="Batal" variant="outlined" @click="rejectCallback" />
                </div>
            </div>
        </template>
    </ConfirmDialog>

    <aside
        :class="[
            'fixed z-20 flex h-screen flex-col border-r border-gray-200 bg-white/90 shadow-xl backdrop-blur transition-all duration-300 dark:border-gray-700 dark:bg-gray-900/80',
            isOpen ? 'w-64' : 'w-20',
        ]"
    >
        <!-- Logo + Toggle -->
        <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
            <img src="/img/logo_kota.png" alt="Logo" class="h-8 w-8" />
            <span class="text-dark font-bold dark:text-gray-100" v-if="isOpen">Judul Aplikasi</span>
            <button @click="emit('toggle')" class="rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                <Menu class="h-5 w-5 text-gray-700 dark:text-gray-300" />
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 space-y-2 p-3">
            <!-- Dashboard -->
            <template v-if="role === 'admin'">
                <a
                    :href="route('admin.dashboard')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWith('admin.dashboard')
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <Home class="h-5 w-5" />
                    <span v-if="isOpen">Dashboard</span>
                </a>

                <a
                    :href="route('admin.kuesioner.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWith('admin.kuesioner.index')
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <FileText class="h-5 w-5" />
                    <span v-if="isOpen">Kuesioner</span>
                </a>
            </template>

            <template v-if="role === 'kepala'">
                <a
                    :href="route('kelapa.dashboard')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWith('kepala.dashboard')
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <Home class="h-5 w-5" />
                    <span v-if="isOpen">Dashboard</span>
                </a>
            </template>

            <!-- Settings -->
            <a
                :href="route('settings.index')"
                :class="[
                    'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                    isActiveStartsWith('settings.index')
                        ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                ]"
            >
                <Settings class="h-5 w-5" />
                <span v-if="isOpen">Settings</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="border-t p-3 dark:border-gray-700">
            <button
                @click="confirmLogout"
                class="flex w-full cursor-pointer items-center justify-center gap-3 rounded-lg px-3 py-2 text-red-600 transition hover:bg-red-100 dark:hover:bg-red-900/40"
            >
                <Power class="h-5 w-5" />
                <span v-if="isOpen">Logout</span>
            </button>
        </div>
    </aside>
</template>
