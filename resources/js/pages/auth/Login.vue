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
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-b from-[#fdfdfc] to-[#f6f6f4] p-4 dark:from-gray-900 dark:to-gray-800">
        <div
            class="w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl transition-colors duration-300 dark:border-gray-700 dark:bg-gray-900"
        >
            <!-- Grid layout -->
            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Logo -->
                <div class="flex items-center justify-center border-gray-200 bg-gray-50 p-6 md:border-r dark:border-gray-700 dark:bg-gray-800">
                    <img src="/img/logo_kota.png" alt="Logo" class="h-32 object-contain md:h-48" />
                </div>

                <!-- Form -->
                <div class="flex flex-col justify-center p-8">
                    <h2 class="mb-6 text-center text-2xl font-bold text-gray-900 dark:text-gray-100">Login</h2>
                    <div class="flex flex-col gap-4">
                        <!-- Username -->
                        <div class="flex flex-col gap-1">
                            <FloatLabel variant="in">
                                <InputText id="username" :invalid="!!form.errors.username" v-model="form.username" class="w-full" />
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
                                    inputClass="w-full"
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
                            unstyled
                            label="Login"
                            class="flex w-full cursor-pointer items-center justify-center gap-2 rounded bg-blue-100 px-3 py-2 font-semibold text-gray-700 transition-colors hover:bg-gray-100 dark:bg-blue-900/40 dark:text-blue-400 dark:hover:bg-blue-600/40"
                            :loading="form.processing"
                        />
                    </div> 
                </div>
            </div>
        </div>
    </div>
</template>
