<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    steps: { value: string; label: string }[];
}>();

const step = ref(localStorage.getItem('currentStep') || props.steps[0].value);

watch(step, (val) => {
    localStorage.setItem('currentStep', val);
});

const goTo = (val: string) => {
    step.value = val;
};

onMounted(() => {
    const saved = localStorage.getItem('currentStep');
    if (saved) step.value = saved;
});

defineExpose({ step, goTo });
</script>

<template>
    <div class="flex flex-col">
        <!-- Judul Step Aktif -->
        <h2 class="mb-4 text-center text-xl font-bold text-blue-600 transition-all duration-300 dark:text-blue-400">
            {{ props.steps.find((s) => s.value === step)?.label }}
        </h2>

        <!-- Panel content -->
        <div class="animate-fade-in">
            <slot :step="step" :goTo="goTo"></slot>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>
