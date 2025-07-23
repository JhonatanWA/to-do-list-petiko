<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    user_id: ''
});

const page = usePage();

const users = computed(() => {
    return page.props.users;
});

function submit() {
    form.post(route('tasks.store'));
}
</script>

<template>
    <Head title="Nova Tarefa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Nova Tarefa</h2>
        </template>

        <div class="py-6 px-4 mx-auto">
            <form @submit.prevent="submit" class="bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                    />
                    <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                        Não é permitido campo vazio
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea
                        v-model="form.description"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                        rows="4"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                    <input
                        v-model="form.due_date"
                        type="date"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                    />
                    <div v-if="form.errors.due_date" class="text-red-600 text-sm mt-1">
                        <p class="text-red-600 text-sm mt-1">A data de vencimento deve ser igual ou posterior à data atual.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Usuário</label>
                    <select
                        v-model="form.user_id"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                    >
                        <option value="">Selecione um usuário</option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.user_id" class="text-red-600 text-sm mt-1">
                        Selecione um usuário
                    </div>
                </div>

                <div class="flex justify-end items-center gap-4">
                    <Link
                        :href="route('tasks.index')"
                        class="text-gray-600 hover:underline"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded"
                        :disabled="form.processing"
                    >
                        Criar
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
