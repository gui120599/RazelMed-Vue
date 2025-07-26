<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue'; // Verifique se o caminho do AppLayout está correto (Layouts ou layouts)
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'; // Verifique se o caminho é Components ou components
import { Head, Link } from '@inertiajs/vue3'; // Importe usePage para acessar flash messages
import { Button, buttonVariants } from '@/components/ui/button'; // Verifique se o caminho é Components ou components
// Importar a função deleteDashboard do seu composable
import { deleteDashboard } from '@/composables/useDashboard'; // <--- AQUI!
import DashboardCreateDialog from './DashboardCreateDialog.vue'; // Importe o novo componente de dialog
import DashboardEditDialog from './DashboardEditDialog.vue'; // Importe o novo componente de dialog de edição
// Importe as novas funções de formatação
import { formatCpfCnpj, formatPhone } from '@/helpers/formatters'; // <--- Nova importação aqui!
import { Layers2 } from 'lucide-vue-next';

defineProps({
    // A prop agora é 'dashboards' e terá a estrutura de paginação do Laravel
    dashboards: {
        type: Object, // Laravel paginate retorna um objeto com 'data', 'links', etc.
        required: true
    },
    institutions: {
        type: Array,
        required: true
    }
})

const breadcrumbs = [
    {
        title: 'Home',
        href: '/home',
    },
    {
        title: 'Dashboards', // Novo breadcrumb para Dashboardes
        href: '/dashboards',
    },
];

</script>

<template>

    <Head title="Dashboards" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <DashboardCreateDialog :institutions="institutions" />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="sr-only lg:not-sr-only">Icon</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead class="sr-only lg:not-sr-only">Iframe Link</TableHead>
                        <TableHead>Institution</TableHead>
                        <TableHead class="w-[120px]">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="dashboard in dashboards" :key="dashboard.id">
                        <TableCell class="sr-only lg:not-sr-only"><component :is="Layers2" /></TableCell>
                        <TableCell>{{ dashboard.name }}</TableCell>
                        <TableCell class="sr-only lg:not-sr-only">{{ dashboard.iframe_link || 'N/A' }}</TableCell>
                        <TableCell>{{ dashboard.institution.name || 'N/A' }}</TableCell>
                        <TableCell class="space-x-2">
                            <DashboardEditDialog :dashboard="dashboard" :institutions="institutions" />
                            <Button variant="destructive" @click="deleteDashboard(dashboard.id)">Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>


        </div>
    </AppLayout>
</template>