<script setup>
import { ref, watch } from 'vue'; // Importar watch
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox'; // Para a opção de remover foto

import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps({
    dashboard: { // Recebe o objeto dashboarde para edição
        type: Object,
        required: true,
    },
    institutions:{
        type: Array,
        required: true,
    }
});

const isDialogOpen = ref(false);

const form = useForm({
    _method: 'put', // Importante para o Laravel interpretar como PUT
    name: props.dashboard.name,
    iframe_link: props.dashboard.iframe_link,
    institution_id: props.dashboard.institution_id,
    icon: props.dashboard.icon,
});

// Observar mudanças na prop 'dashboard' para resetar o formulário
// Isso é útil se o mesmo dialog for reutilizado para diferentes dashboardes sem recarregar a página.
watch(() => props.dashboard, (newDashboard) => {
    form.name = newDashboard.name;
    form.iframe_link = newDashboard.iframe_link;
    form.institution_id = newDashboard.institution_id;
    form.icon = newDashboard.icon;
    form.clearErrors(); // Limpa quaisquer erros anteriores
}, { deep: true });


const handleSubmit = () => {
    // Usamos form.post e _method: 'put' para lidar com upload de arquivos
    // Inertia.js requer que envios com arquivos usem o método POST, e o _method spoofing para PUT/PATCH
    form.post(route('dashboards.update', props.dashboard.id), {
        forceFormData: true, // Necessário para enviar arquivos
        onSuccess: () => {
            toast.success('Dashboard updated successfully!');
            form.clearErrors();
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Failed to update dashboard. Please check the form.');
        },
    });
};

// Expose the dialog state for parent component to control
defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="default">Edit</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Edit Dashboard</DialogTitle>
                <DialogDescription>Atualize os dados do dashboarde.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="grid lg:col-span-2 w-full gap-2">
                                <Label for="name">Nome</Label>
                                <Input id="name" autocomplete="off" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid lg:col-span-1 w-full gap-2">
                                <Label for="icon">Icone</Label>
                                <Input type="text" autocomplete="off" id="icon" v-model="form.icon" />
                                <InputError :message="form.errors.icon" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="institution_id">Instituição</Label>
                                <Select id="institution_id" v-model="form.institution_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione a Institutição"></SelectValue>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="institution in institutions" :key="institution.id"
                                            :value="institution.id">{{ institution.name }}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                        <div class="grid w-full gap-2">
                            <Label for="iframe_link">Iframe Link</Label>
                            <Input id="iframe_link" autocomplete="off" v-model="form.iframe_link" />
                            <InputError :message="form.errors.iframe_link" />
                        </div>

                        <div class="flex justify-end items-center gap-4">
                            <Button type="button" variant="outline" @click="isDialogOpen = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Dashboard' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>