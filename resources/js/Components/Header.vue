<script setup lang="ts">
import type { AppPageProps } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import { defineEmits, ref } from 'vue';

const emit = defineEmits<{ (e: 'toggle'): void }>();

const page = usePage<AppPageProps>();
const user = page.props.auth.user;

const isDark = ref(false);
</script>

<template>
    <header
        class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white/70 px-6 py-4 shadow-sm backdrop-blur dark:border-gray-700 dark:bg-gray-900/70"
    >
        <!-- Left: toggle + title -->
        <div class="flex items-center gap-3">
            <button @click="emit('toggle')" class="rounded-lg p-2 hover:bg-gray-100 md:hidden dark:hover:bg-gray-800">
                <Menu class="h-5 w-5 text-gray-700 dark:text-gray-300" />
            </button>
            <h1 class="text-lg font-bold text-gray-800 dark:text-gray-100">DISDIKBUD Kota Jayapura</h1>
        </div>

        <!-- User -->
        <div class="flex items-center gap-3">
            <span class="hidden font-medium text-gray-700 md:inline dark:text-gray-200">{{ user.name }}</span>
            <img
                :src="user.avatar ?? `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`"
                alt="User Avatar"
                class="h-9 w-9 rounded-full border-2 border-gray-300 shadow-sm dark:border-gray-600"
            />
        </div>
    </header>
</template>
