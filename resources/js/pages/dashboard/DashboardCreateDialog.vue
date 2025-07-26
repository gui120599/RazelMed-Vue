<script setup>
import { ref, onUnmounted } from 'vue'; // Importar onUnmounted
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';


defineProps({
    institutions: {
        type: Array,
        required: true
    }
})

const isDialogOpen = ref(false); // Estado para controlar a abertura/fechamento do dialog

const form = useForm({
    name: '',
    iframe_link: '',
    institution_id: '',
    icon: '',
});

const handleSubmit = () => {
    // Crie uma cópia dos dados do formulário para manipulação
    const dataToSend = { ...form.data() };

    form.post(route('dashboards.store'), {
        data: dataToSend, // <--- Passe os dados limpos aqui!
        forceFormData: true, // Importante para enviar arquivos (profile_photo)
        onSuccess: () => {
            toast.success('Dashboard created successfully!');
            form.reset(); // Limpa o formulário após o sucesso
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Failed to create dashboard. Please check the form.');
        },
    });
};

// Expose the dialog state and form for parent component if needed (e.g., to open programmatically)
defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="outline">Add Dashboard</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>New Dashboard</DialogTitle>
                <DialogDescription>Insira os dados do novo dashboard.</DialogDescription>
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
                                <InputError :message="form.errors.institution_id" />
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