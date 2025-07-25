<script setup lang="js">
import AppLayout from '@/layouts/AppLayout.vue';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3'; // Importe useForm para o delete

import InstitutionCreateDialog from './InstitutionCreateDialog.vue'; // Certifique-se de que estes paths estão corretos
import InstitutionEditDialog from './InstitutionEditDialog.vue'; // Certifique-se de que estes paths estão corretos

// Importe as funções de formatação (CNPJ apenas para Instituição)
import { formatCpfCnpj } from '@/helpers/formatters';

// Para o delete
const form = useForm({});

const deleteInstitution = (id) => {
    if (confirm('Tem certeza que deseja deletar esta instituição?')) {
        form.delete(route('institutions.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Instituição deletada com sucesso!');
            },
            onError: (errors) => {
                alert('Erro ao deletar instituição: ' + Object.values(errors).join('\n'));
            }
        });
    }
};

defineProps({
    institutions: {
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
        title: 'Instituições',
        href: route('admin.institutions.index'),
    },
];
</script>

<template>
    <Head title="Gerenciar Instituições" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mt-3 flex justify-between items-center">
                <InstitutionCreateDialog />
            </div>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Logo</TableHead>
                        <TableHead>Nome</TableHead>
                        <TableHead>CNPJ</TableHead>
                        <TableHead class="w-[120px]">Ações</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="institution in institutions.data" :key="institution.id">
                        <TableCell>
                            <img v-if="institution.profile_photo_path" :src="`/storage/${institution.profile_photo_path}`"
                                alt="Logo da Instituição" class="w-10 h-10 rounded-full object-cover" />
                            <div v-else
                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                Sem Logo</div>
                        </TableCell>
                        <TableCell>{{ institution.name }}</TableCell>
                        <TableCell>{{ formatCpfCnpj(institution.cnpj) || 'N/A' }}</TableCell>
                        <TableCell class="space-x-2">
                            <InstitutionEditDialog :institution="institution" />
                            <Button variant="destructive" @click="deleteInstitution(institution.id)">Deletar</Button>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>