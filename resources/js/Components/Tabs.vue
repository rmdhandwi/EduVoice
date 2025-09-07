<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

interface Tab {
    key: string; 
    label: string;
    component?: any;
}

const props = defineProps<{
    tabs: Tab[];
    storageKey?: string; // key untuk localStorage (default: "activeTab")
}>();

const activeTab = ref<string>('');

const STORAGE_KEY = props.storageKey || 'activeTab';

// Set tab pertama sebagai default
onMounted(() => {
    const savedTab = localStorage.getItem(STORAGE_KEY);
    if (savedTab && props.tabs.some((t) => t.key === savedTab)) {
        activeTab.value = savedTab;
    } else {
        activeTab.value = props.tabs[0]?.key || '';
    }
});

// Simpan ke localStorage setiap kali tab berubah
watch(activeTab, (newTab) => {
    localStorage.setItem(STORAGE_KEY, newTab);
});
</script>

<template>
    <div class="w-full">
        <!-- Tab Header -->
        <div class="flex gap-2 border-gray-300 dark:border-gray-700">
            <button
                v-for="tab in tabs"
                :key="tab.key"
                @click="activeTab = tab.key"
                class="-mb-px border-b-2 px-4 py-2 text-sm font-medium transition-colors cursor-pointer"
                :class="[
                    activeTab === tab.key
                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                        : 'border-transparent text-gray-600 hover:text-blue-500 dark:text-gray-300 dark:hover:text-blue-400',
                ]"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Tab Content -->
        <div class="pt-3 mt-4">
            <slot :name="activeTab" />
            <component v-if="tabs.find((t) => t.key === activeTab)?.component" :is="tabs.find((t) => t.key === activeTab)?.component" />
        </div>
    </div>
</template>
