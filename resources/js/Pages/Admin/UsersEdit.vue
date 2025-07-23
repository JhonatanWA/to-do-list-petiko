<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    roles: Array,
    user_role_id: Number,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role_id: props.user_role_id,
});

function updateUser() {
    form.put(route('admin.users.update', props.user.id));
}
</script>

<template>
    <Head title="Editar Usuário" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar Usuário</h2>
        </template>

        <div class="p-6 bg-white rounded shadow">
            <form @submit.prevent="updateUser">
                <div class="mb-4">
                    <label>Name</label>
                    <input v-model="form.name" type="text" class="border rounded w-full" />
                    <div v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</div>
                </div>

                <div class="mb-4">
                    <label>Email</label>
                    <input v-model="form.email" type="email" class="border rounded w-full" />
                    <div v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</div>
                </div>

                <div class="mb-4">
                    <label>Role</label>
                    <select v-model="form.role_id" class="border rounded w-full">
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                    </select>
                    <div v-if="form.errors.role" class="text-red-600">{{ form.errors.role }}</div>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Salvar</button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
