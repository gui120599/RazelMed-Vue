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
} from '@/components/ui/select';

const props = defineProps({
    dashboard: {
        type: Object, // Recebe o objeto do dashboard a ser editado
        required: true,
    },
});

const isDialogOpen = ref(false);

// Access page props for institutions
const page = usePage();
const allInstitutions = page.props.allInstitutions || []; // Assuming this is passed to Dashboard index

const form = useForm({
    _method: 'put', // Importante para simular um PUT/PATCH
    name: '',
    iframe_link: '',
    icon: '',
    institution_id: '',
});

const fillForm = () => {
    form.name = props.dashboard.name;
    form.iframe_link = props.dashboard.iframe_link;
    form.icon = props.dashboard.icon;
    form.institution_id = props.dashboard.institution_id;
};

watch(isDialogOpen, (isOpen) => {
    if (isOpen) {
        fillForm();
    } else {
        form.reset();
    }
});
onMounted(fillForm);
watch(() => props.dashboard, fillForm, { deep: true });

const handleSubmit = () => {
    form.post(route('dashboards.update', props.dashboard.id), { // Rota para update de dashboards
        onSuccess: () => {
            toast.success('Dashboard atualizado com sucesso!');
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao atualizar dashboard. Verifique o formulário.');
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
        <DialogContent class="lg:min-w-[700px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Editar Dashboard</DialogTitle>
                <DialogDescription>Edite os dados do dashboard.</DialogDescription>
            </DialogHeader>
            <Card>
                <CardContent class="space-y-3">
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="grid w-full gap-2">
                                <Label for="name">Nome do Dashboard</Label>
                                <Input id="name" autocomplete="off" v-model="form.name" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="iframe_link">Link do Iframe</Label>
                                <Input id="iframe_link" autocomplete="off" v-model="form.iframe_link"
                                    placeholder="Ex: https://lookerstudio.google.com/embed/report/..." />
                                <InputError :message="form.errors.iframe_link" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="icon">Ícone (Nome do Lucide Icon)</Label>
                                <Input id="icon" autocomplete="off" v-model="form.icon"
                                    placeholder="Ex: LayoutDashboard, ChartBar" />
                                <InputError :message="form.errors.icon" />
                            </div>
                            <div class="grid w-full gap-2">
                                <Label for="institution_id">Instituição</Label>
                                <Select v-model="form.institution_id">
                                    <SelectTrigger class="w-full">
                                        <SelectValue :placeholder="form.institution_id ? allInstitutions.find(i => i.id === form.institution_id)?.name : 'Selecione uma instituição'" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectLabel>Instituições</SelectLabel>
                                            <SelectItem v-for="institution in allInstitutions" :key="institution.id" :value="institution.id">
                                                {{ institution.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.institution_id" />
                            </div>
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