<script setup>
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    tasks: Object, 
    search: {
        type: String,
        default: '',
    },
    isAdmin: { 
        type: Boolean,
        default: false,
    },
});

const searchQuery = ref(props.search);

watch(searchQuery, debounce((value) => {
    router.get(route('tasks.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

function deleteTask(id) {
    router.delete(route('tasks.destroy', id), {
        onBefore: () => confirm('Tem certeza que deseja excluir esta tarefa?'),
    });
}

function toggleTaskCompletion(task) {
    router.put(route('tasks.update', task.id), {
        title: task.title,
        description: task.description,
        due_date: task.due_date,
        is_completed: !task.is_completed,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
}


function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); 
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}


const pendingTasks = computed(() => {
    return props.tasks.data.filter(task => !task.is_completed);
});


const completedTasks = computed(() => {
    return props.tasks.data.filter(task => task.is_completed);
});
</script>

<template>
    <Head title="Tarefas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Minhas Tarefas</h2>
        </template>

        <div class="py-6 px-4 max-w-4xl mx-auto">
            <div class="mb-6">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Pesquisar tarefas por título ou descrição..."
                    class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Tarefas Pendentes</h3>
                <Link
                    v-if="isAdmin"
                    :href="route('tasks.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200"
                >
                    + Nova Tarefa
                </Link>
            </div>

            <div v-if="pendingTasks.length === 0" class="text-gray-600">
                Nenhuma tarefa pendente encontrada.
            </div>

            <ul v-else class="space-y-4">
                <li
                    v-for="task in pendingTasks"
                    :key="task.id"
                    class="p-4 bg-white rounded-lg shadow-md flex justify-between items-center"
                >
                    <div>
                        <h4 class="text-md font-semibold">
                            {{ task.title }}
                            <span v-if="task.due_date" class="text-gray-500 text-sm font-normal">
                                - {{ formatDate(task.due_date) }}
                            </span>
                            <span v-if="isAdmin && task.user" class="text-gray-400 text-xs font-normal ml-2">
                                (Para: {{ task.user.name }})
                            </span>
                        </h4>
                        <p class="text-sm text-gray-600" v-if="task.description">
                            {{ task.description }}
                        </p>
                        <p
                            class="text-xs mt-1 text-yellow-600"
                        >
                            Pendente
                        </p>
                    </div>

                    <div class="flex gap-2 items-center">
                        <button
                            @click="toggleTaskCompletion(task)"
                            class="text-sm px-3 py-1 rounded-md bg-green-500 text-white hover:bg-green-600 transition-colors duration-200"
                        >
                            Concluir
                        </button>
                        <Link
                            v-if="isAdmin"
                            :href="route('tasks.edit', task.id)"
                            class="text-sm px-3 py-1 rounded-md bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200"
                        >
                            Editar
                        </Link>
                        <button
                            @click="deleteTask(task.id)"
                            class="text-sm px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
                        >
                            Excluir
                        </button>
                    </div>
                </li>
            </ul>

            <hr class="bg-gray-300 h-px my-8">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Tarefas Concluídas</h3>
            </div>

            <div v-if="completedTasks.length === 0" class="text-gray-600">
                Nenhuma tarefa concluída.
            </div>

            <ul v-else class="space-y-4">
                <li
                    v-for="task in completedTasks"
                    :key="task.id"
                    class="p-4 bg-white rounded-lg shadow-md flex justify-between items-center opacity-70"
                >
                    <div>
                        <del class="text-md font-semibold text-gray-700">
                            {{ task.title }}
                            <span v-if="task.due_date" class="text-gray-500 text-sm font-normal">
                                - {{ formatDate(task.due_date) }}
                            </span>
                            <span v-if="isAdmin && task.user" class="text-gray-400 text-xs font-normal ml-2">
                                (Para: {{ task.user.name }})
                            </span>
                        </del>
                        <p class="text-sm text-gray-500" v-if="task.description">
                            <del>{{ task.description }}</del>
                        </p>
                        <p
                            class="text-xs mt-1 text-green-600"
                        >
                            Concluída
                        </p>
                    </div>

                    <div class="flex gap-2 items-center">
                        <button
                            @click="toggleTaskCompletion(task)"
                            class="text-sm px-3 py-1 rounded-md bg-yellow-500 text-white hover:bg-yellow-600 transition-colors duration-200"
                        >
                            Reabrir
                        </button>
                        <Link
                            v-if="isAdmin"
                            :href="route('tasks.edit', task.id)"
                            class="text-sm px-3 py-1 rounded-md bg-blue-500 text-white hover:bg-blue-600 transition-colors duration-200"
                        >
                            Editar
                        </Link>
                        <button
                            @click="deleteTask(task.id)"
                            class="text-sm px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
                        >
                            Excluir
                        </button>
                    </div>
                </li>
            </ul>

            <!-- Links de Paginação -->
            <div v-if="props.tasks.links.length > 3" class="flex justify-center mt-8 space-x-2">
                <template v-for="(link, key) in props.tasks.links" :key="key">
                    <div
                        v-if="link.url === null"
                        class="px-4 py-2 text-gray-400 text-sm leading-4 border rounded-md"
                        v-html="link.label"
                    ></div>
                    <Link
                        v-else
                        :href="link.url"
                        class="px-4 py-2 text-sm leading-4 border rounded-md hover:bg-indigo-500 hover:text-white focus:border-indigo-500 focus:text-indigo-500"
                        :class="{ 'bg-indigo-600 text-white': link.active }"
                        v-html="link.label"
                    ></Link>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
