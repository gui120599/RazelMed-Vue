<script setup lang="js">
import { ref, onUnmounted } from 'vue';
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

const isDialogOpen = ref(false);
const profilePhotoPreviewUrl = ref(null); // Para o logo da instituição

const form = useForm({
    name: '',
    cnpj: '', // Campo para CNPJ
    address: '',
    email: '',
    phone: '',
    profile_photo: null, // Para o arquivo de imagem do logo
});

const handleSubmit = () => {
    const dataToSend = { ...form.data() };

    // Limpar máscaras antes de enviar
    if (dataToSend.cnpj) {
        dataToSend.cnpj = dataToSend.cnpj.replace(/[^0-9]/g, '');
    }
    if (dataToSend.phone) {
        dataToSend.phone = dataToSend.phone.replace(/[^0-9]/g, '');
    }

    form.post(route('institutions.store'), { // Rota para store de instituições
        data: dataToSend,
        forceFormData: true, // Importante para enviar arquivos
        onSuccess: () => {
            toast.success('Instituição criada com sucesso!');
            form.reset();
            profilePhotoPreviewUrl.value = null;
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao criar instituição. Verifique o formulário.');
        },
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.profile_photo = file;

    if (file) {
        profilePhotoPreviewUrl.value = URL.createObjectURL(file);
    } else {
        profilePhotoPreviewUrl.value = null;
    }
};

onUnmounted(() => {
    if (profilePhotoPreviewUrl.value) {
        URL.revokeObjectURL(profilePhotoPreviewUrl.value);
    }
});

defineExpose({
    isDialogOpen,
    form
});
</script>

<template>
    <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
            <Button variant="outline">Adicionar Instituição</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Nova Instituição</DialogTitle>
                <DialogDescription>Insira os dados da nova instituição.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="flex flex-col items-center gap-2">
                            <div v-if="profilePhotoPreviewUrl" class="mb-2">
                                <img :src="profilePhotoPreviewUrl" alt="Logo Preview"
                                    class="w-24 h-24 rounded-full object-cover">
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="profile_photo">Logo da Instituição</Label>
                                <Input id="profile_photo" type="file" @change="handleFileChange" />
                                <InputError :message="form.errors.profile_photo" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="grid lg:col-span-2 w-full gap-2">
                                <Label for="name">Nome da Instituição</Label>
                                <Input id="name" autocomplete="off" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid lg:col-span-1 w-full gap-2">
                                <Label for="cnpj">CNPJ</Label>
                                <Input id="cnpj" autocomplete="off" v-model="form.cnpj"
                                    v-mask="'##.###.###/####-##'" /> <InputError :message="form.errors.cnpj" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="phone">Telefone</Label>
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
                            <Label for="address">Endereço</Label>
                            <Textarea id="address" autocomplete="off" v-model="form.address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="flex justify-end items-center gap-4">
                            <Button type="button" variant="outline" @click="isDialogOpen = false">Cancelar</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Salvando...' : 'Salvar Instituição' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>