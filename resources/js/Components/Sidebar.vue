<script setup lang="ts">
import type { AppPageProps } from '@/types';
import { router, usePage } from '@inertiajs/vue3';
import { useWindowSize } from '@vueuse/core';
import { FileText, Home, Menu, Power, Settings, Star, UsersRound } from 'lucide-vue-next';
import { useConfirm, useToast } from 'primevue';
import { computed, defineEmits, defineProps } from 'vue';

const props = defineProps<{ isOpen: boolean }>();
const emit = defineEmits<{ (e: 'toggle'): void }>();

const page = usePage<AppPageProps>();
const role = page.props.auth.user.role;

// cek layar mobile
const { width } = useWindowSize();
const isMobile = computed(() => width.value < 768);

const isActiveStartsWithAny = (prefixes: string[]) => {
    return prefixes.some((prefix) => route().current(`${prefix}*`));
};

const confirm = useConfirm();
const toast = useToast();

const confirmLogout = () => {
    confirm.require({
        group: 'logoutConfirm',
        header: 'Konfirmasi Logout',
        message: 'Apakah kamu yakin ingin keluar?',
        accept: () => router.post(route('auth.logout')),
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
    <Toast class="z-[999]" />

    <!-- Dialog konfirmasi logout -->
    <ConfirmDialog group="logoutConfirm">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="bg-surface-0 dark:bg-surface-900 flex flex-col items-center rounded-lg p-8 shadow-lg">
                <!-- Icon Logout -->
                <div class="-mt-20 inline-flex h-24 w-24 items-center justify-center rounded-full bg-red-500 text-white">
                    <i class="pi pi-sign-out !text-4xl"></i>
                </div>

                <!-- Judul dan pesan -->
                <span class="mt-6 mb-2 block text-2xl font-bold">{{ message.header }}</span>
                <p class="mb-0 text-center text-gray-700 dark:text-gray-300">{{ message.message }}</p>

                <!-- Tombol aksi -->
                <div class="mt-6 flex items-center gap-2">
                    <Button size="small" label="Ya, Logout" severity="danger" @click="acceptCallback" />
                    <Button size="small" label="Batal" severity="secondary" variant="outlined" @click="rejectCallback" />
                </div>
            </div>
        </template>
    </ConfirmDialog>

    <!-- Overlay untuk mobile -->
    <div v-if="isMobile && isOpen" class="fixed inset-0 z-40 bg-black/40 md:hidden" @click="emit('toggle')" />

    <!-- Sidebar -->
    <aside
        :class="[
            'fixed z-50 flex h-screen flex-col border-r border-gray-200 bg-white/90 shadow-xl backdrop-blur transition-all duration-300 dark:border-gray-700 dark:bg-gray-900/90',
            isMobile ? (isOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full') : isOpen ? 'w-64' : 'w-20',
        ]"
    >
        <!-- Logo + Toggle -->
        <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
            <img src="/img/logo_kota.png" alt="Logo" class="h-8 w-8" />
            <span class="text-dark font-bold dark:text-gray-100" v-if="isOpen"> SKM DISDIKBUD </span>
            <button @click="emit('toggle')" class="rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                <Menu class="h-5 w-5 text-gray-700 dark:text-gray-300" />
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 space-y-2 overflow-y-auto p-3">
            <template v-if="role === 'admin'">
                <a
                    :href="route('admin.dashboard')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWithAny(['admin.dashboard'])
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
                        isActiveStartsWithAny(['admin.kuesioner.index', 'admin.kuesioner.*'])
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <FileText class="h-5 w-5" />
                    <span v-if="isOpen">Kuesioner</span>
                </a>

                <a
                    :href="route('admin.responden.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWithAny(['admin.responden.index', 'admin.responden.*'])
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <UsersRound class="h-5 w-5" />
                    <span v-if="isOpen">Responden</span>
                </a>

                <a
                    :href="route('admin.ikm.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWithAny(['admin.ikm.index', 'admin.ikm.*'])
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <Star class="h-5 w-5" />
                    <span v-if="isOpen">IKM</span>
                </a>
            </template>

            <template v-if="role === 'kepala'">
                <a
                    :href="route('kepala.dashboard')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWithAny(['kepala.dashboard'])
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <Home class="h-5 w-5" />
                    <span v-if="isOpen">Dashboard</span>
                </a>

                <a
                    :href="route('kepala.laporan.index')"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                        isActiveStartsWithAny(['kepala.laporan.index', 'kepala.laporan.*'])
                            ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                    ]"
                >
                    <FileText class="h-5 w-5" />
                    <span v-if="isOpen">Laporan</span>
                </a>
            </template>

            <!-- Settings -->
            <a
                :href="route('settings.index')"
                :class="[
                    'flex items-center gap-3 rounded-lg px-3 py-2 transition',
                    isActiveStartsWithAny(['settings.index'])
                        ? 'bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800',
                ]"
            >
                <Settings class="h-5 w-5" />
                <span v-if="isOpen">Settings</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="border-t border-gray-300 p-3 dark:border-gray-700">
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
