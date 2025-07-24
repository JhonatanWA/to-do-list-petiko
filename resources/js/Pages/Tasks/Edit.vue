<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
});

function formatToInputDate(dateString) {
    if (!dateString) return '';
    return dateString.substring(0, 10);
}

const form = useForm({
    title: props.task.title,
    description: props.task.description,
    is_completed: props.task.is_completed,
    due_date: formatToInputDate(props.task.due_date),
    user_id: props.task.user_id,
});

const page = usePage();

const users = computed(() => {
    return page.props.users;
});

function update() {
    form.put(route('tasks.update', props.task.id));
}
</script>

<template>
    <Head title="Editar Tarefa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Editar Tarefa</h2>
        </template>

        <div class="py-6 px-4 max-w-2xl mx-auto">
            <form @submit.prevent="update" class="bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Título</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm"
                    />
                    <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">
                        {{ form.errors.title }}
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
                        {{ form.errors.description }}
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
                    <label class="block text-sm font-medium text-gray-700">Responsável</label>
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
                        {{ form.errors.user_id }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            v-model="form.is_completed"
                            class="rounded border-gray-300"
                            true-value="1"   false-value="0"
                        />
                        <span class="ml-2 text-sm text-gray-700">Marcar como concluída</span>
                    </label>
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
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
