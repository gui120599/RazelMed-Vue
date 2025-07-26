<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue'; // Verifique se o caminho do AppLayout está correto (Layouts ou layouts)
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'; // Verifique se o caminho é Components ou components
import { Head, Link } from '@inertiajs/vue3'; // Importe usePage para acessar flash messages
import { Button, buttonVariants } from '@/components/ui/button'; // Verifique se o caminho é Components ou components
// Importar a função deleteInstitution do seu composable
import { deleteInstitution } from '@/composables/useInstitution'; // <--- AQUI!
import InstitutionCreateDialog from './InstitutionCreateDialog.vue'; // Importe o novo componente de dialog
import InstitutionEditDialog from './InstitutionEditDialog.vue'; // Importe o novo componente de dialog de edição
// Importe as novas funções de formatação
import { formatCpfCnpj, formatPhone } from '@/helpers/formatters'; // <--- Nova importação aqui!

defineProps({
    // A prop agora é 'institutions' e terá a estrutura de paginação do Laravel
    institutions: {
        type: Object, // Laravel paginate retorna um objeto com 'data', 'links', etc.
        required: true
    }
})

const breadcrumbs = [
    {
        title: 'Home',
        href: '/home',
    },
    {
        title: 'Institutions', // Novo breadcrumb para Institutiones
        href: '/institutions',
    },
];

</script>

<template>

    <Head title="Institutions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <InstitutionCreateDialog />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Photo</TableHead>
                        <TableHead>Name</TableHead>
                        <TableHead class="sr-only md:not-sr-only">Email</TableHead>
                        <TableHead class="sr-only md:not-sr-only">Phone</TableHead>
                        <TableHead>CNPJ</TableHead>
                        <TableHead class="sr-only lg:not-sr-only">Address</TableHead>
                        <TableHead class="w-[120px]">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="institution in institutions" :key="institution.id">
                        <TableCell>
                            <img v-if="institution.profile_photo_path" :src="`/storage/${institution.profile_photo_path}`"
                                alt="Profile Photo" class="w-10 h-10 rounded-full object-cover" />
                            <div v-else
                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                No Photo</div>
                        </TableCell>
                        <TableCell >{{ institution.name }}</TableCell>
                        <TableCell class="sr-only md:not-sr-only">{{ institution.email || 'N/A' }}</TableCell>
                        <TableCell class="sr-only md:not-sr-only">
                            {{ formatPhone(institution.phone) }}
                        </TableCell>
                        <TableCell class="">{{ formatCpfCnpj(institution.cnpj) || 'N/A' }}</TableCell>
                        <TableCell class="max-w-[200px] truncate sr-only lg:not-sr-only">{{ institution.address || 'N/A' }}</TableCell>
                        <TableCell class="space-x-2">
                            <InstitutionEditDialog :institution="institution" />
                            <Button variant="destructive" @click="deleteInstitution(institution.id)">Delete</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>


        </div>
    </AppLayout>
</template>