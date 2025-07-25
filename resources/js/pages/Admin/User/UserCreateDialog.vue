<script setup lang="js">
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { Checkbox } from '@/components/ui/checkbox'; // For is_super_admin
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
} from '@/components/ui/select'; // For Institution selection

const isDialogOpen = ref(false);

// Access page props for all institutions and all dashboards for assigning permissions
const page = usePage();
const allInstitutions = page.props.allInstitutions || [];
const allDashboards = page.props.allDashboards || []; // You'll need to pass this from UserController@indexAdmin

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_super_admin: false,
    // Para relacionamentos many-to-many, passamos IDs
    institutions: [], // Array de IDs de instituições
    accessible_dashboards: [], // Array de IDs de dashboards
});

const handleSubmit = () => {
    // Note: User creation typically involves a POST to /register route or a custom admin route.
    // For simplicity, we'll use a route like 'users.store' which you'd define in routes/web.php
    // (e.g., Route::post('/users', [UserController::class, 'store'])->name('users.store'))
    form.post(route('users.store'), { // Assumindo que você terá uma rota 'users.store' no seu UserController
        onSuccess: () => {
            toast.success('Usuário criado com sucesso!');
            form.reset();
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao criar usuário. Verifique o formulário.');
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
            <Button variant="outline">Adicionar Usuário</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[800px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Novo Usuário</DialogTitle>
                <DialogDescription>Insira os dados do novo usuário e suas permissões.</DialogDescription>
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
                                <Label for="password">Senha</Label>
                                <Input id="password" type="password" v-model="form.password" />
                                <InputError :message="form.errors.password" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="password_confirmation">Confirmar Senha</Label>
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
                                {{ form.processing ? 'Salvando...' : 'Criar Usuário' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>