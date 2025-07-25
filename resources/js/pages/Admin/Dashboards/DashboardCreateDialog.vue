<script setup lang="js">
import { ref, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3'; // Import usePage to get institutions
import { toast } from 'vue-sonner';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea'; // If you need a description field
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
} from '@/components/ui/select'; // For Institution selection

const isDialogOpen = ref(false);

// Access page props for institutions
const page = usePage();
// Assuming you pass `allInstitutions` from the controller to the Dashboard index page
// e.g., return Inertia::render('Admin/Dashboards/Index', ['dashboards' => ..., 'allInstitutions' => Institution::all()]);
const allInstitutions = page.props.allInstitutions || []; // Provide a fallback empty array


const form = useForm({
    name: '',
    iframe_link: '',
    icon: '', // Optionally, if dashboards have icons (e.g., Lucide icon names)
    institution_id: '', // To link the dashboard to an institution
});

const handleSubmit = () => {
    form.post(route('dashboards.store'), { // Rota para store de dashboards
        onSuccess: () => {
            toast.success('Dashboard criado com sucesso!');
            form.reset();
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
            toast.error('Falha ao criar dashboard. Verifique o formulário.');
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
            <Button variant="outline">Adicionar Dashboard</Button>
        </DialogTrigger>
        <DialogContent class="lg:min-w-[700px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Novo Dashboard</DialogTitle>
                <DialogDescription>Insira os dados do novo dashboard.</DialogDescription>
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
                                        <SelectValue placeholder="Selecione uma instituição" />
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
                                {{ form.processing ? 'Salvando...' : 'Salvar Dashboard' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </DialogContent>
    </Dialog>
</template>