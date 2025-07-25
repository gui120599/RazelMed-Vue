<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';

import UserEditDialog from './UserEditDialog.vue'; // Certifique-se de que o path está correto
// Importe a função de formatação de telefone, se aplicável a usuários
import { formatPhone } from '@/helpers/formatters';

// Para o delete
const form = useForm({});

const deleteUser = (id) => {
    if (confirm('Tem certeza que deseja deletar este usuário?')) {
        form.delete(route('users.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Usuário deletado com sucesso!');
            },
            onError: (errors) => {
                alert('Erro ao deletar usuário: ' + Object.values(errors).join('\n'));
            }
        });
    }
};

defineProps({
    users: {
        type: Object, // Laravel paginate retorna um objeto com 'data', 'links', etc.
        required: true
    }
});

const breadcrumbs = [
    {
        title: 'Home',
        href: '/home',
    },
    {
        title: 'Usuários',
        href: route('admin.users.index'),
    },
];
</script>

<template>
    <Head title="Gerenciar Usuários" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <h2 class="text-2xl font-bold">Usuários</h2>
                </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nome</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Super Admin</TableHead>
                        <TableHead>Instituição Preferencial</TableHead>
                        <TableHead>Instituições Acessíveis</TableHead>
                        <TableHead>Dashboards Acessíveis</TableHead>
                        <TableHead class="w-[120px]">Ações</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="user in users.data" :key="user.id">
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell>{{ user.email }}</TableCell>
                        <TableCell>{{ user.is_super_admin ? 'Sim' : 'Não' }}</TableCell>
                        <TableCell>{{ user.preferred_institution?.name || 'N/A' }}</TableCell>
                        <TableCell>
                            <span v-if="user.institutions && user.institutions.length">
                                {{ user.institutions.map(inst => inst.name).join(', ') }}
                            </span>
                            <span v-else>Nenhuma</span>
                        </TableCell>
                        <TableCell>
                            <span v-if="user.accessible_dashboards && user.accessible_dashboards.length">
                                {{ user.accessible_dashboards.map(dash => dash.name).join(', ') }}
                            </span>
                            <span v-else>Nenhum</span>
                        </TableCell>
                        <TableCell class="space-x-2">
                            <UserEditDialog :user="user" />
                            <Button variant="destructive" @click="deleteUser(user.id)">Deletar</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <div class="mt-4 flex justify-between items-center" v-if="users.links">
                <div class="flex space-x-2">
                    <Link
                        v-for="link in users.links"
                        :key="link.label"
                        :href="link.url"
                        class="px-3 py-1 rounded-md"
                        :class="{ 'bg-blue-500 text-white': link.active, 'bg-gray-200': !link.active }"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>