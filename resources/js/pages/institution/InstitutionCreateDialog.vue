<script setup>
import { ref, onUnmounted } from 'vue'; // Importar onUnmounted
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const isDialogOpen = ref(false); // Estado para controlar a abertura/fechamento do dialog

// Adicione uma nova ref para a URL de preview da foto
const profilePhotoPreviewUrl = ref(null);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    cnpj: '',
    address: '',
    profile_photo: null, // Para o arquivo de imagem
});

const handleSubmit = () => {
    // Crie uma cópia dos dados do formulário para manipulação
    const dataToSend = { ...form.data() };

    // Limpar a máscara do CPF/CNPJ antes de enviar
    if (dataToSend.cnpj) {
        dataToSend.cnpj = dataToSend.cnpj.replace(/[^0-9]/g, '');
    }

    // Limpar a máscara do Telefone antes de enviar
    if (dataToSend.phone) {
        dataToSend.phone = dataToSend.phone.replace(/[^0-9]/g, '');
    }

    form.post(route('institutions.store'), {
        data: dataToSend, // <--- Passe os dados limpos aqui!
        forceFormData: true, // Importante para enviar arquivos (profile_photo)
        onSuccess: () => {
            toast.success('Institution created successfully!');
            form.reset(); // Limpa o formulário após o sucesso
            profilePhotoPreviewUrl.value = null; // Limpa o preview após o sucesso
            isDialogOpen.value = false; // Fecha o dialog
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Failed to create institution. Please check the form.');
        },
    });
};

// Função para lidar com a seleção do arquivo de imagem
const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.profile_photo = file;

    if (file) {
        // Cria uma URL temporária para o preview da imagem
        profilePhotoPreviewUrl.value = URL.createObjectURL(file);
    } else {
        profilePhotoPreviewUrl.value = null; // Limpa o preview se nenhum arquivo for selecionado
    }
};

// Hook para limpar a URL de preview quando o componente é desmontado (boa prática)
onUnmounted(() => {
    if (profilePhotoPreviewUrl.value) {
        URL.revokeObjectURL(profilePhotoPreviewUrl.value);
    }
});

// Expose the dialog state and form for parent component if needed (e.g., to open programmatically)
defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="outline">Add Institution</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>New Institution</DialogTitle>
                <DialogDescription>Insira os dados do novo institutione.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="flex flex-col items-center gap-2">
                            <div v-if="profilePhotoPreviewUrl" class="mb-2">
                                <img :src="profilePhotoPreviewUrl" alt="Profile Preview"
                                    class="w-24 h-24 rounded-full object-cover">
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="profile_photo">Profile Photo</Label>
                                <Input id="profile_photo" type="file" @change="handleFileChange" />
                                <InputError :message="form.errors.profile_photo" />
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
                                {{ form.processing ? 'Saving...' : 'Save Institution' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>