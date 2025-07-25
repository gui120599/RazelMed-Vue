<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';

import DashboardCreateDialog from './DashboardCreateDialog.vue'; // Certifique-se de que estes paths estão corretos
import DashboardEditDialog from './DashboardEditDialog.vue'; // Certifique-se de que estes paths estão corretos
import { Link2Icon } from 'lucide-vue-next'; // Ícone para link de iframe

// Para o delete
const form = useForm({});

const deleteDashboard = (id) => {
    if (confirm('Tem certeza que deseja deletar este dashboard?')) {
        form.delete(route('dashboards.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Dashboard deletado com sucesso!');
            },
            onError: (errors) => {
                alert('Erro ao deletar dashboard: ' + Object.values(errors).join('\n'));
            }
        });
    }
};

defineProps({
    dashboards: {
        type: Object,
        required: true
    }
});

const breadcrumbs = [
    {
        title: 'Home',
        href: '/home',
    },
    {
        title: 'Dashboards',
        href: '/',
    },
];
</script>

<template>
    <Head title="Gerenciar Dashboards" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <DashboardCreateDialog />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Nome</TableHead>
                        <TableHead>Link Iframe</TableHead>
                        <TableHead>Instituição</TableHead>
                        <TableHead>Ícone</TableHead>
                        <TableHead class="w-[120px]">Ações</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="dashboard in dashboards.data" :key="dashboard.id">
                        <TableCell>{{ dashboard.name }}</TableCell>
                        <TableCell>
                            <a v-if="dashboard.iframe_link" :href="dashboard.iframe_link" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1">
                                <Link2Icon class="w-4 h-4" /> Visualizar
                            </a>
                            <span v-else>N/A</span>
                        </TableCell>
                        <TableCell>{{ dashboard.institution?.name || 'N/A' }}</TableCell>
                        <TableCell>{{ dashboard.icon || 'N/A' }}</TableCell>
                        <TableCell class="space-x-2">
                            <!--<DashboardEditDialog :dashboard="dashboard" />-->
                            <Button variant="destructive" @click="deleteDashboard(dashboard.id)">Deletar</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!--<div class="mt-4 flex justify-between items-center" v-if="dashboards.links">
                <div class="flex space-x-2">
                    <Link
                        v-for="link in dashboards.links"
                        :key="link.label"
                        :href="link.url"
                        class="px-3 py-1 rounded-md"
                        :class="{ 'bg-blue-500 text-white': link.active, 'bg-gray-200': !link.active }"
                        v-html="link.label"
                    />
                </div>
            </div>-->
        </div>
    </AppLayout>
</template>