<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { router  } from '@inertiajs/vue3';
    import PrimaryButton from '@/Components/PrimaryButton.vue';

    const props = defineProps({
        users: Array,
        roles: Array,
    });

    function deleteUser(id) {
        if (confirm('Tem certeza que deseja excluir?')) {
            router.delete(route('users.destroy', id));
        }
    }
</script>
<template>
    <Head title="Gerenciar Usuários" />
    <AuthenticatedLayout>
            <a :href="route('users.create')">
            <PrimaryButton 
                class="mb-4"
            >Criar Novo Usuário</PrimaryButton>
            </a>
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <h1 class="text-2xl font-bold mb-4 mt-4 text-center">Controle de Usuários</h1>
                <table class="table-auto w-full border text-left">
                    <thead>
                    <tr class="bg-purple-100">
                        <th class="p-2">Nome</th>
                        <th class="p-2">Email</th>
                        <th class="p-2">Acesso</th>
                        <th class="p-2">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-t hover:bg-purple-50">
                        <td class="p-2">{{ user.name }}</td>
                        <td class="p-2">{{ user.email }}</td>
                        <td class="p-2">{{ user.roles.map(r => r.name).join(', ') || 'Sem Acesso' }}</td>
                        <td class="px-4 py-2">
                            <a :href="route('users.edit', user.id)" class="text-sm text-blue-600 mr-2">Editar</a>
                                |
                            <button @click="deleteUser(user.id)" class="text-sm text-red-600 ml-2">Excluir</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
    </AuthenticatedLayout>
</template>
