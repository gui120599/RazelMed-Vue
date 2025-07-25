<script setup lang="js">
import { ref, onMounted, watch, onUnmounted } from 'vue'; // Importar watch e onMounted
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

const props = defineProps({
    institution: {
        type: Object, // Recebe o objeto da instituição a ser editada
        required: true,
    },
});

const isDialogOpen = ref(false);
const profilePhotoPreviewUrl = ref(null); // Para o logo da instituição

const form = useForm({
    _method: 'put', // Importante para simular um PUT/PATCH com Inertia
    name: '',
    cnpj: '',
    address: '',
    email: '',
    phone: '',
    profile_photo: null, // Para o novo arquivo de imagem
});

// Função para preencher o formulário com os dados da instituição
const fillForm = () => {
    form.name = props.institution.name;
    form.cnpj = props.institution.cnpj;
    form.address = props.institution.address;
    form.email = props.institution.email;
    form.phone = props.institution.phone;

    // Se houver uma foto de perfil existente, defina o preview
    if (props.institution.profile_photo_path) {
        profilePhotoPreviewUrl.value = `/storage/${props.institution.profile_photo_path}`;
    } else {
        profilePhotoPreviewUrl.value = null;
    }
};

// Quando o dialog abre, preencher o formulário
watch(isDialogOpen, (isOpen) => {
    if (isOpen) {
        fillForm();
    } else {
        // Resetar o formulário e preview ao fechar, para não exibir dados antigos
        form.reset();
        profilePhotoPreviewUrl.value = null;
    }
});

// Disparar o preenchimento inicial caso o componente já esteja montado e o dialog abra.
// Ou se o prop 'institution' mudar (útil se o modal for reutilizado para diferentes instituições sem recarregar o componente)
onMounted(fillForm);
watch(() => props.institution, fillForm, { deep: true });


const handleSubmit = () => {
    const dataToSend = { ...form.data() }; // Cópia dos dados

    // Limpar máscaras antes de enviar
    if (dataToSend.cnpj) {
        dataToSend.cnpj = dataToSend.cnpj.replace(/[^0-9]/g, '');
    }
    if (dataToSend.phone) {
        dataToSend.phone = dataToSend.phone.replace(/[^0-9]/g, '');
    }

    form.post(route('institutions.update', props.institution.id), { // Rota para update de instituições
        data: dataToSend,
        forceFormData: true, // Importante para enviar arquivos (PUT/PATCH com files exige FormData)
        onSuccess: () => {
            toast.success('Instituição atualizada com sucesso!');
            isDialogOpen.value = false; // Fecha o dialog
            // Não resetar o form aqui para que a tabela possa atualizar com os novos dados
            // e então o watch de isDialogOpen se encarregará de resetar ao fechar
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao atualizar instituição. Verifique o formulário.');
        },
    });
};

const handleFileChange = (event) => {
    const file = event.target.files[0];
    form.profile_photo = file;

    if (file) {
        profilePhotoPreviewUrl.value = URL.createObjectURL(file);
    } else {
        // Se nenhum arquivo for selecionado, volta para a foto existente ou nula
        profilePhotoPreviewUrl.value = props.institution.profile_photo_path ? `/storage/${props.institution.profile_photo_path}` : null;
    }
};

onUnmounted(() => {
    // Limpar a URL de preview temporária ao desmontar
    if (profilePhotoPreviewUrl.value && profilePhotoPreviewUrl.value.startsWith('blob:')) {
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
            <Button variant="outline" size="sm">Editar</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Instituição</DialogTitle>
                <DialogDescription>Edite os dados da instituição.</DialogDescription>
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
                                <small class="text-muted-foreground">Deixe em branco para manter a logo atual.</small>
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
                                <Input id="cnpj" autocomplete="off" v-model="form.cnpj" v-mask="'##.###.###/####-##'" />
                                <InputError :message="form.errors.cnpj" />
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
                                {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>