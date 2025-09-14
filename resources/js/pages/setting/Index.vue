<script lang="ts" setup>
import Tabs from '@/Components/Tabs.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { AppPageProps } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Password from './Password.vue';

const page = usePage<AppPageProps>();
const user = page.props.auth.user;

// Toggle edit state
const isEditing = ref(false);

const flash = computed(() => page.props.flash);

// Form edit profile
const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
});

// Handle submit form
const submit = () => {
    form.put(route('settings.update'), {
        preserveScroll: true,
        preserveState: true,
        onError: () => {
            isEditing.value = true; // Tetap buka form jika ada error
        },
        onSuccess: () => {
            isEditing.value = false; // Tutup form setelah sukses
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        },
    });
};

const breadcrumb = [{ label: 'Setting', route: 'settings.index', global: true }];
</script>

<template>
    <DashboardLayout title="Settings" :breadcrumb-items="breadcrumb">
        <Head title="Setting" />

        <Tabs
            :tabs="[
                { key: 'profile', label: 'Profile' },
                { key: 'password', label: 'Password', component: Password },
            ]"
            storage-key="dashboard-tabs"
        >
            <!-- Profile Tab -->
            <template #profile>
                <div class="mx-auto max-w-5xl space-y-6 rounded-2xl bg-white animate-fade-in delay-300 p-6 shadow dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Kiri: Detail -->
                        <div class="space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-semibold text-white">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        {{ user.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ user.email }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Username</p>
                                    <p class="text-gray-800 dark:text-gray-200">{{ user.username }}</p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Role</p>
                                    <p class="text-gray-800 dark:text-gray-200">
                                        {{ user.role ?? 'User' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Dibuat</p>
                                    <p class="text-gray-800 dark:text-gray-200">
                                        {{
                                            new Date(user.created_at).toLocaleDateString('id-ID', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })
                                        }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Terakhir diperbarui</p>
                                    <p class="text-gray-800 dark:text-gray-200">
                                        {{
                                            new Date(user.updated_at).toLocaleDateString('id-ID', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Tombol Edit -->
                            <div class="flex justify-start">
                                <Button
                                    :icon="isEditing ? 'pi pi-times' : 'pi pi-pencil'"
                                    :label="isEditing ? 'Batal' : 'Edit Profile'"
                                    class="p-button-sm"
                                    :class="isEditing ? 'p-button-danger' : 'p-button-outlined'"
                                    @click="isEditing = !isEditing"
                                />
                            </div>
                        </div>

                        <!-- Kanan: Form Edit -->
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Profile</h3>

                            <div class="space-y-4">
                                <!-- Nama -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                                    <InputText
                                        id="name"
                                        v-model="form.name"
                                        :invalid="!!form.errors.name"
                                        class="mt-1 w-full"
                                        :disabled="!isEditing"
                                    />
                                    <Message v-if="form.errors.name" variant="simple" severity="error">{{ form.errors.name }}</Message>
                                </div>

                                <!-- Username -->
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                                    <InputText
                                        id="username"
                                        v-model="form.username"
                                        :invalid="!!form.errors.username"
                                        class="mt-1 w-full"
                                        :disabled="!isEditing"
                                    />
                                    <Message v-if="form.errors.username" variant="simple" severity="error">{{ form.errors.username }}</Message>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <InputText
                                        id="email"
                                        v-model="form.email"
                                        :invalid="!!form.errors.email"
                                        class="mt-1 w-full"
                                        :disabled="!isEditing"
                                    />
                                    <Message v-if="form.errors.email" variant="simple" severity="error">{{ form.errors.email }}</Message>
                                </div>

                                <!-- Tombol Simpan -->
                                <div class="flex justify-end">
                                    <Button
                                        label="Simpan"
                                        icon="pi pi-check"
                                        class="p-button-sm p-button-success"
                                        :loading="form.processing"
                                        :disabled="!isEditing"
                                        @click="submit"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Tabs>
    </DashboardLayout>
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
    animation: fade-in 0.8s ease-out both;
}
.delay-100 {
    animation-delay: 0.1s;
}
.delay-200 {
    animation-delay: 0.2s;
}
.delay-300 {
    animation-delay: 0.3s;
}
</style>

