<script setup lang="js">
import { ref, computed, watch, nextTick } from 'vue'; // Importe nextTick
import { useForm, usePage, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { UserPlus, UserMinus, Search, RefreshCcw, Users } from 'lucide-vue-next';

// Shadcn UI Components
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Table,
    TableHeader,
    TableRow,
    TableHead,
    TableBody,
    TableCell,
} from '@/components/ui/table';

const props = defineProps({
    institution: {
        type: Object, // A instituição que estamos gerenciando
        required: true,
    },
    // NOVO: Adicione uma prop 'open' para controlar o diálogo de fora (Opção A)
    open: {
        type: Boolean,
        default: false,
    },
});

// NOVO: Emit para atualizar a prop 'open' no componente pai
const emit = defineEmits(['update:open']);

const page = usePage();
const allUsers = computed(() => page.props.allUsers || []);

// Use internalOpen para o v-model do Dialog
const internalOpen = ref(props.open);
const searchTerm = ref('');

// A lista de usuários **atual** da instituição, que será atualizada
const currentInstitutionUsers = ref([]);

// --- Lógica de Sincronização e Watchers ---

// 1. Sincroniza a prop 'open' externa com o estado interno do diálogo
watch(() => props.open, (newVal) => {
    internalOpen.value = newVal;
});

// 2. Sincroniza o estado interno do diálogo com a prop 'open' externa
watch(internalOpen, (newVal) => {
    emit('update:open', newVal);
});

// 3. Função para popular currentInstitutionUsers.value
// Esta função precisa ser chamada quando a prop 'institution' muda OU quando o diálogo abre.
const updateInstitutionUsers = () => {
    // Garanta que `institution.users` esteja carregado no backend.
    // O Inertia.js garante que `props.institution` já está atualizado
    // se o `router.reload({ only: ['institutions'] })` for bem-sucedido.
    currentInstitutionUsers.value = props.institution.users || [];
};

// 4. Watch para quando o diálogo abre. Isso garante que a lista é carregada na primeira abertura.
watch(internalOpen, (isOpen) => {
    if (isOpen) {
        // Quando o diálogo abre, atualiza a lista de usuários da instituição
        updateInstitutionUsers();
        searchTerm.value = ''; // Limpa a busca ao abrir
    }
});

// 5. Watch para mudanças na prop 'institution'.
// Isso é crucial porque 'institution' pode ser atualizada após um router.reload.
// O { deep: true } garante que mudanças nas propriedades aninhadas (como 'users') sejam detectadas.
watch(() => props.institution, (newVal, oldVal) => {
    // Só atualiza se o diálogo estiver aberto ou se a instituição mudou significativamente
    // O {deep: true} já cuida da mudança de users
    if (internalOpen.value) { // Só atualiza se o diálogo estiver aberto para evitar processamento desnecessário
        updateInstitutionUsers();
    }
}, { deep: true });

// --- Computed Properties para as Tabelas ---

const usersWithAccess = computed(() => {
    const term = searchTerm.value.toLowerCase();
    return currentInstitutionUsers.value.filter(user =>
        user.name.toLowerCase().includes(term) ||
        user.email.toLowerCase().includes(term)
    );
});

const usersWithoutAccess = computed(() => {
    const usersWithAccessIds = new Set(currentInstitutionUsers.value.map(user => user.id));
    const term = searchTerm.value.toLowerCase();

    return allUsers.value.filter(user =>
        !usersWithAccessIds.has(user.id) &&
        (user.name.toLowerCase().includes(term) || user.email.toLowerCase().includes(term))
    );
});

// --- Lógica de Formulário e Ações (Attach/Detach) ---

const form = useForm({
    user_id: null,
});

const attachUser = (userId) => {
    form.user_id = userId;
    form.post(route('institutions.users.attach', props.institution.id), {
        onSuccess: () => {
            toast.success('Usuário adicionado com sucesso!');
            // Chamar router.reload para atualizar a prop 'institutions' no parent
            router.reload({ only: ['institutions'] });
            // O watcher de props.institution (com deep: true) + internalOpen.value
            // garantirá que currentInstitutionUsers.value seja atualizado.
            // O nextTick é para garantir que a atualização do DOM aconteça
            // após as reatividades do Vue serem processadas.
            nextTick(() => {
                updateInstitutionUsers(); // Força a atualização após o reload e a reatividade do Vue
            });
        },
        onError: (errors) => {
            console.error('Erro ao adicionar usuário:', errors);
            toast.error('Falha ao adicionar usuário. Verifique o console.');
        },
        onFinish: () => {
            // O reset do formulário pode ser aqui ou em outro lugar
        }
    });
};

const detachUser = (userId) => {
    if (!confirm('Tem certeza que deseja remover este usuário desta instituição?')) {
        return;
    }

    form.user_id = userId;
    form.delete(route('institutions.users.detach', props.institution.id), {
        onSuccess: () => {
            toast.success('Usuário removido com sucesso!');
            router.reload({ only: ['institutions'] }); // Recarrega as props da página
            nextTick(() => {
                updateInstitutionUsers(); // Força a atualização
            });
        },
        onError: (errors) => {
            console.error('Erro ao remover usuário:', errors);
            toast.error('Falha ao remover usuário. Verifique o console.');
        },
    });
};

// Exponha o método para abrir o diálogo programaticamente (ainda útil se você quiser um botão dedicado)
const openDialog = () => {
    internalOpen.value = true;
};

defineExpose({
    openDialog, // Mantenha isso se quiser a capacidade de abrir via ref do pai
    // Não exponha isDialogOpen diretamente se estiver usando v-model:open
});
</script>

<template>
    <Dialog v-model:open="internalOpen">
        <DialogTrigger as-child>
            <Button variant="outline" size="sm" title="PERMISSÕES DE USUÁRIOS">
                <Users />
            </Button>
        </DialogTrigger>
        <DialogContent class="lg:max-w-[1200px] max-h-[90vh] overflow-y-auto">
            <DialogHeader>
                <DialogTitle>Gerenciar Usuários da Instituição: {{ institution.name }}</DialogTitle>
                <DialogDescription>
                    Adicione ou remova usuários que têm acesso a esta instituição.
                </DialogDescription>
            </DialogHeader>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Usuários Disponíveis (Sem Acesso)</h3>
                    <div class="relative mb-4">
                        <Input v-model="searchTerm" placeholder="Buscar usuário por nome ou email..." class="pl-10" />
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="h-[400px] overflow-y-auto border rounded-md">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead class="text-right">Ação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="usersWithoutAccess.length === 0">
                                    <TableCell colspan="3" class="text-center text-muted-foreground">
                                        Nenhum usuário disponível.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="user in usersWithoutAccess" :key="user.id">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="outline" size="sm" @click="attachUser(user.id)"
                                            :disabled="form.processing">
                                            <UserPlus class="h-4 w-4 mr-1" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-2">Usuários com Acesso à Instituição</h3>
                    <div class="relative mb-4">
                        <Input v-model="searchTerm" placeholder="Buscar usuário por nome ou email..." class="pl-10" />
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="h-[400px] overflow-y-auto border rounded-md">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead class="text-right">Ação</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="usersWithAccess.length === 0">
                                    <TableCell colspan="3" class="text-center text-muted-foreground">
                                        Nenhum usuário com acesso direto.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="user in usersWithAccess" :key="user.id">
                                    <TableCell>{{ user.name }}</TableCell>
                                    <TableCell>{{ user.email }}</TableCell>
                                    <TableCell class="text-right">
                                        <Button variant="destructive" size="sm" @click="detachUser(user.id)"
                                            :disabled="form.processing">
                                            <UserMinus class="h-4 w-4 mr-1" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <Button variant="outline" @click="internalOpen = false">Fechar</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>