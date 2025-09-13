<!-- resources/js/Components/AppLogo.vue -->
<template>
    <div class="flex items-center gap-3">
        <img src="img/logo_kota.png" alt="Logo" class="h-10 w-10 select-none" />
        <span v-if="withText" class="text-lg font-bold">SKM DISDIKBUD</span>
    </div>
</template>

<script setup lang="ts">
defineProps<{ withText?: boolean }>();
</script>
