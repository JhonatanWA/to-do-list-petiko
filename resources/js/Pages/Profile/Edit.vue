<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Perfil
            </h2>
        </template>
            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 my-5"
            >
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 my-5"
            >
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8 my-5"
            >
                <DeleteUserForm class="max-w-xl" />
            </div>
    </AuthenticatedLayout>
</template>
