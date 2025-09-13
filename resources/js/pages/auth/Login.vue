<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { onMounted } from 'vue';
import { AppPageProps } from '../../types/index';

const page = usePage<AppPageProps>();

console.log(page.props);

const form = useForm({
    username: '',
    password: '',
});

const toast = useToast();

const showToast = (severity: 'success' | 'warning' | 'error', summary: string, detail: string) => {
    toast.add({
        severity,
        summary,
        detail,
        life: 3000,
    });
};

// Baca flash message dari Laravel saat mount
onMounted(() => {
    if (page.props.flash?.success) {
        showToast('success', 'Berhasil', page.props.flash.success);
    }

    if (page.props.flash?.warning) {
        showToast('warning', 'Peringatan', page.props.flash.warning);
    }

    if (page.props.flash?.error) {
        showToast('error', 'Gagal', page.props.flash.error);
    }
});

const handleLogin = () => {
    form.post(route('login.post'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Toast />
    <Head title="Login" />

    <div
        class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 p-6 dark:from-gray-950 dark:via-gray-900 dark:to-gray-800"
    >
        <div
            class="w-full max-w-4xl overflow-hidden rounded-3xl border border-white/30 bg-white/60 shadow-2xl backdrop-blur-xl transition-colors duration-300 dark:border-gray-700 dark:bg-gray-900/70"
        >
            <!-- Grid layout -->
            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Logo & tagline -->
                <div
                    class="flex flex-col items-center justify-center gap-6 border-gray-200 bg-gradient-to-br from-blue-600 to-purple-600 p-8 text-center text-white md:border-r dark:border-gray-700"
                >
                    <img src="/img/logo_kota.png" alt="Logo" class="h-28 w-28 border-white" />
                    <div>
                        <h1 class="text-3xl font-extrabold">SKM DISDIKBUD</h1>
                        <p class="mt-2 text-sm text-blue-100">Survey Kepuasan Masyarakat</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="flex flex-col justify-center p-10">
                    <h2 class="mb-6 text-center text-2xl font-bold text-gray-900 dark:text-gray-100">Login ke Akun Anda</h2>

                    <div class="flex flex-col gap-6">
                        <!-- Username -->
                        <div class="flex flex-col gap-1">
                            <FloatLabel variant="in">
                                <InputText
                                    id="username"
                                    :invalid="!!form.errors.username"
                                    v-model="form.username"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                />
                                <label for="username">Username</label>
                            </FloatLabel>
                            <Message v-if="form.errors.username" severity="error" size="small" variant="simple">
                                {{ form.errors.username }}
                            </Message>
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col gap-1">
                            <FloatLabel variant="in">
                                <Password
                                    id="password"
                                    :invalid="!!form.errors.password"
                                    v-model="form.password"
                                    toggleMask
                                    :feedback="false"
                                    class="w-full"
                                    inputClass="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                                />
                                <label for="password">Password</label>
                            </FloatLabel>
                            <Message v-if="form.errors.password" severity="error" size="small" variant="simple">
                                {{ form.errors.password }}
                            </Message>
                        </div>

                        <!-- Tombol -->
                        <Button
                            @click="handleLogin"
                            type="button"
                            label="Login"
                            unstyled
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-[1.02] hover:from-blue-700 hover:to-purple-700 active:scale-95"
                            :loading="form.processing"
                        />

                        <div class="flex items-center justify-center text-sm text-gray-600 dark:text-gray-200">
                            <a href="/" class="transition-all duration-100 hover:text-blue-700 hover:scale-90"> Kembali ke Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
