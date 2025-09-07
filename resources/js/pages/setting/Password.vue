<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import Password from 'primevue/password';

// Form ubah password
const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Submit handler
const submit = () => {
    form.put(route('settings.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('current_password', 'password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <div class="max-w-lg rounded-xl bg-white p-6 shadow dark:bg-gray-800">
        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-gray-100">Ubah Password</h2>

        <div class="space-y-4">
            <!-- Password Sekarang -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> Password Sekarang </label>
                <Password
                    id="current_password"
                    toggle-mask
                    :feedback="false"
                    v-model="form.current_password"
                    :invalid="!!form.errors.current_password"
                    class="mt-1 w-full"
                    inputClass="w-full"
                />
                <Message v-if="form.errors.current_password" severity="error" variant="simple">
                    {{ form.errors.current_password }}
                </Message>
            </div>

            <!-- Password Baru -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> Password Baru </label>
                <Password
                    id="password"
                    toggle-mask
                    :feedback="false"
                    v-model="form.password"
                    :invalid="!!form.errors.password"
                    class="mt-1 w-full"
                    inputClass="w-full"
                />
                <Message v-if="form.errors.password" severity="error" variant="simple">
                    {{ form.errors.password }}
                </Message>
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300"> Konfirmasi Password </label>
                <Password
                    id="password_confirmation"
                    toggle-mask
                    :feedback="false"
                    v-model="form.password_confirmation"
                    :invalid="!!form.errors.password_confirmation"
                    class="mt-1 w-full"
                    inputClass="w-full"
                />
                <Message v-if="form.errors.password_confirmation" severity="error" variant="simple">
                    {{ form.errors.password_confirmation }}
                </Message>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end">
                <Button label="Simpan" icon="pi pi-check" class="p-button-sm p-button-success" :loading="form.processing" @click="submit" />
            </div>
        </div>
    </div>
</template>
