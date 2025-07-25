<script setup lang="js">
import { ref, onMounted, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'; // For Institution/Dashboard selection

const props = defineProps({
    user: {
        type: Object, // Recebe o objeto do usuário a ser editado
        required: true,
    },
});

const isDialogOpen = ref(false);

// Access page props for all institutions and all dashboards for assigning permissions
const page = usePage();
const allInstitutions = page.props.allInstitutions || [];
const allDashboards = page.props.allDashboards || [];


const form = useForm({
    _method: 'put', // Importante para simular um PUT/PATCH
    name: '',
    email: '',
    password: '', // Campo opcional para redefinir senha
    password_confirmation: '', // Campo opcional para redefinir senha
    is_super_admin: false,
    prefer_institution_id: null, // Pode ser null
    institutions: [], // Array de IDs de instituições (multi-select)
    accessible_dashboards: [], // Array de IDs de dashboards (multi-select)
});

const fillForm = () => {
    form.name = props.user.name;
    form.email = props.user.email;
    form.is_super_admin = props.user.is_super_admin;
    form.prefer_institution_id = props.user.prefer_institution_id;
    // Mapeie os objetos relacionados para um array de IDs para o multi-select
    form.institutions = props.user.institutions ? props.user.institutions.map(inst => inst.id) : [];
    form.accessible_dashboards = props.user.accessible_dashboards ? props.user.accessible_dashboards.map(dash => dash.id) : [];

    // Resetar campos de senha ao abrir, para que não enviem valores vazios
    form.password = '';
    form.password_confirmation = '';
};

watch(isDialogOpen, (isOpen) => {
    if (isOpen) {
        fillForm();
    } else {
        form.reset(); // Limpa o formulário ao fechar
    }
});
onMounted(fillForm);
watch(() => props.user, fillForm, { deep: true }); // Observa mudanças no prop 'user'

const handleSubmit = () => {
    form.post(route('users.update', props.user.id), { // Rota para update de usuários
        onSuccess: () => {
            toast.success('Usuário atualizado com sucesso!');
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao atualizar usuário. Verifique o formulário.');
        },
    });
};

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
                <DialogTitle>Editar Usuário</DialogTitle>
                <DialogDescription>Edite os dados do usuário e suas permissões.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="name">Nome</Label>
                                <Input id="name" autocomplete="off" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="email">Email</Label>
                                <Input type="email" autocomplete="off" id="email" v-model="form.email" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="password">Nova Senha (opcional)</Label>
                                <Input id="password" type="password" v-model="form.password"
                                    placeholder="Deixe em branco para não alterar" />
                                <InputError :message="form.errors.password" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="password_confirmation">Confirmar Nova Senha</Label>
                                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" />
                                <InputError :message="form.errors.password_confirmation" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox id="is_super_admin" v-model:checked="form.is_super_admin" />
                            <Label for="is_super_admin">É Super Admin?</Label>
                        </div>
                        <InputError :message="form.errors.is_super_admin" />

                        <div class="grid w-full gap-2">
                            <Label for="prefer_institution_id">Instituição Preferencial</Label>
                            <Select v-model="form.prefer_institution_id">
                                <SelectTrigger class="w-full">
                                    <SelectValue :placeholder="form.prefer_institution_id ? allInstitutions.find(i => i.id === form.prefer_institution_id)?.name : 'Nenhuma'" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Instituições</SelectLabel>
                                        <SelectItem :value="null">Nenhuma</SelectItem> <SelectItem v-for="inst in allInstitutions" :key="inst.id" :value="inst.id">
                                            {{ inst.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.prefer_institution_id" />
                        </div>


                        <div class="grid w-full gap-2">
                            <Label for="institutions">Instituições Acessíveis</Label>
                             <Select v-model="form.institutions" multiple>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecione instituições" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Instituições</SelectLabel>
                                        <SelectItem v-for="inst in allInstitutions" :key="inst.id" :value="inst.id">
                                            {{ inst.name }}
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.institutions" />
                        </div>

                        <div class="grid w-full gap-2">
                            <Label for="accessible_dashboards">Dashboards Acessíveis</Label>
                            <Select v-model="form.accessible_dashboards" multiple>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecione dashboards" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Dashboards</SelectLabel>
                                        <SelectItem v-for="dash in allDashboards" :key="dash.id" :value="dash.id">
                                            {{ dash.name }} ({{ dash.institution?.name || 'Sem Instituição' }})
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.accessible_dashboards" />
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