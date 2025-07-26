<script setup>
import { ref, watch } from 'vue'; // Importar watch
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
    institution: { // Recebe o objeto institutione para edição
        type: Object,
        required: true,
    },
});

const isDialogOpen = ref(false);

const form = useForm({
    _method: 'put', // Importante para o Laravel interpretar como PUT
    name: props.institution.name,
    email: props.institution.email,
    phone: props.institution.phone,
    cnpj: props.institution.cnpj,
    address: props.institution.address,
    profile_photo: null, // Para o novo arquivo de imagem
    profile_photo_path: props.institution.profile_photo_path, // Caminho da foto existente
    profile_photo_remove: false, // Flag para indicar remoção da foto existente
});

// Observar mudanças na prop 'institution' para resetar o formulário
// Isso é útil se o mesmo dialog for reutilizado para diferentes institutiones sem recarregar a página.
watch(() => props.institution, (newInstitution) => {
    form.name = newInstitution.name;
    form.email = newInstitution.email;
    form.phone = newInstitution.phone;
    form.cnpj = newInstitution.cnpj;
    form.address = newInstitution.address;
    form.profile_photo = null; // Limpa a nova foto selecionada
    form.profile_photo_path = newInstitution.profile_photo_path; // Atualiza caminho da foto existente
    form.profile_photo_remove = false; // Reseta flag de remoção
    form.clearErrors(); // Limpa quaisquer erros anteriores
}, { deep: true });


const handleSubmit = () => {
    // Usamos form.post e _method: 'put' para lidar com upload de arquivos
    // Inertia.js requer que envios com arquivos usem o método POST, e o _method spoofing para PUT/PATCH
    form.post(route('institutions.update', props.institution.id), {
        forceFormData: true, // Necessário para enviar arquivos
        onSuccess: () => {
            toast.success('Institution updated successfully!');
            // Não resetamos o form para manter os dados atualizados, mas limpamos erros e foto nova
            form.profile_photo = null;
            form.profile_photo_remove = false;
            form.clearErrors();
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Failed to update institution. Please check the form.');
        },
    });
};

const handleFileChange = (event) => {
    form.profile_photo = event.target.files[0];
    if (form.profile_photo) {
        form.profile_photo_remove = false; // Se uma nova foto for selecionada, desmarca a remoção
    }
};

const handleRemovePhotoChange = (checked) => {
    form.profile_photo_remove = checked;
    if (checked) {
        form.profile_photo = null; // Se marcou para remover, limpa qualquer nova foto selecionada
    }
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
                <DialogTitle>Edit Institution</DialogTitle>
                <DialogDescription>Atualize os dados do institutione.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-5" @submit.prevent="handleSubmit">
                        <div class="flex flex-col items-center gap-2">
                            <Label>Profile Photo</Label>
                            <div v-if="form.profile_photo_path && !form.profile_photo_remove" class="mb-2">
                                <img :src="`/storage/${form.profile_photo_path}`" alt="Current Profile Photo"
                                    class="w-24 h-24 rounded-full object-cover">
                            </div>
                            <Input id="profile_photo" type="file" @change="handleFileChange" class="max-w-xs" />
                            <InputError :message="form.errors.profile_photo" />
                            <div class="flex items-center space-x-2 mt-2">
                                <Checkbox id="remove_photo" :checked="form.profile_photo_remove"
                                    @update:checked="handleRemovePhotoChange" />
                                <Label for="remove_photo">Remove current photo</Label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="grid lg:col-span-2 w-full gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" autocomplete="off" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid lg:col-span-1 w-full gap-2">
                                <Label for="cnpj">CPF/CNPJ</Label>
                                <Input id="cnpj" autocomplete="off" v-model="form.cnpj"
                                    v-mask="['###.###.###-##', '##.###.###/####-##']" />
                                <InputError :message="form.errors.cnpj" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="phone">Phone</Label>
                                <Input type="tel" autocomplete="off" id="phone" v-model="form.phone"
                                    v-mask="['(##) ####-####', '(##)# ####-####']" />
                                <InputError :message="form.errors.phone" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="email">Email</Label>
                                <Input type="email" autocomplete="off" id="email" v-model="form.email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>
                        <div class="grid w-full gap-2">
                            <Label for="address">Address</Label>
                            <Textarea id="address" autocomplete="off" v-model="form.address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="flex justify-end items-center gap-4">
                            <Button type="button" variant="outline" @click="isDialogOpen = false">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Updating...' : 'Update Institution' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>