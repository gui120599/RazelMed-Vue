<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue'; // Pode ser removido se não for mais usado
import NavUser from '@/components/NavUser.vue';
// Certifique-se de que SidebarMenuLink está disponível ou ajuste conforme nossa discussão anterior
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { MainNavGroup, type NavItem } from '@/types'; // Se NavMainGroup usar NavItem, mantenha
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Bot, Folder, Headset, LayoutDashboard, LayoutGrid, Settings2, SquareTerminal, SuperscriptIcon, User2, Building2 } from 'lucide-vue-next'; // Adicione Building2 para instituições
import AppLogo from './AppLogo.vue';
// Importe NavMainGroup se ele for um componente que renderiza SidebarGroup/SidebarMenuItem
import NavMainGroup from './NavMainGroup.vue';


// --- Tipos de Dados Dinâmicos (já definidos, apenas para clareza) ---
interface DashboardItem {
    id: number;
    name: string;
    iframe_link: string;
    icon?: string;
    institution_id: number;
}

interface InstitutionItem {
    id: number;
    name: string;
    cnpj?: string;
    profile_photo_path?: string;
    // Note: 'dashboards' aqui pode não ser preenchido diretamente do Inertia,
    // mas sim construído no frontend a partir de accessible_dashboards.
    dashboards?: DashboardItem[];
}

interface UserProps {
    id: number;
    name: string;
    email: string;
    is_super_admin: boolean;
    prefer_institution_id?: number;
    institutions: InstitutionItem[]; // Instituições que o usuário tem acesso
    accessible_dashboards: DashboardItem[]; // Dashboards que o usuário tem permissão granular
    preferred_institution?: InstitutionItem; // A instituição preferencial carregada
}

// Obtenha os props da página, incluindo o usuário autenticado
const page = usePage();
// Usar `as unknown as UserProps` é um bom truque, mas o ideal é que `page.props.auth.user`
// já seja tipado corretamente no seu projeto (ex: via um d.ts para props Inertia).
const user = page.props.auth.user as unknown as UserProps;

// --- Itens de Navegação Estáticos (Home) ---
const staticMainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: route('home'), // Use route() para URLs dinâmicas do Laravel
        icon: LayoutDashboard,
    },
];

// --- Itens de Navegação para Super Admin (Gerenciamento) ---
const superAdminNavItems: NavItem[] = [
    {
        title: 'Gerenciar Instituições',
        href: route('admin.institutions.index'),
        icon: Building2,
    },
    {
        title: 'Gerenciar Dashboards',
        href: route('admin.dashboards.index'),
        icon: LayoutGrid,
    },
    {
        title: 'Gerenciar Usuários',
        href: route('admin.users.index'),
        icon: User2,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Suporte',
        href: '/',
        icon: Headset,
    },
];

// --- Lógica para Construir mainNavGroup Dinamicamente ---
// Isso será o que o NavMainGroup irá consumir
const dynamicNavGroups = computed<MainNavGroup[]>(() => {
    // Se não for super admin e não tiver instituições, não retorna grupos
    if (!user.is_super_admin && (!user.institutions || user.institutions.length === 0)) {
        return [];
    }

    // Se for Super Admin, não usaremos mainNavGroup para dashboards, mas sim os links de gerenciamento
    // que já estão no superAdminNavItems.
    // Opcional: Se quiser que o Super Admin também veja os dashboards agrupados por instituição,
    // ele pode usar a mesma lógica que o usuário comum, mas com todas as instituições/dashboards.
    if (user.is_super_admin) {
        // Para Super Admin, podemos retornar uma estrutura que inclua todos os dashboards,
        // ou você pode decidir que o Super Admin usa apenas os links de gerenciamento.
        // Por enquanto, vamos manter a lógica de grupos de instituição apenas para usuários comuns.
        // Se você quiser que o SA veja agrupado, use a mesma lógica abaixo, mas sem filtros.
        return []; // Super Admin usará `superAdminNavItems`
    }

    // Lógica para usuários comuns: agrupar dashboards por instituição acessível
    const groupedNavItems: MainNavGroup[] = [];
    user.institutions.forEach(institution => {
        const accessibleDashboardsForThisInstitution = user.accessible_dashboards.filter(
            dashboard => dashboard.institution_id === institution.id
        );

        if (accessibleDashboardsForThisInstitution.length > 0) {
            groupedNavItems.push({
                title: institution.name,
                url: '#', // Link da instituição pode ser para uma página de detalhes da instituição ou apenas um placeholder
                icon: Building2, // Ícone padrão para instituição
                items: accessibleDashboardsForThisInstitution.map(dashboard => ({
                    title: dashboard.name,
                    url: route('dashboard.show', { dashboard: dashboard.id }), // Rota para o dashboard específico
                    // icon: dashboard.icon ? dynamicIconComponent(dashboard.icon) : BookOpen, // Se tiver ícones dinâmicos
                })),
            });
        }
    });

    return groupedNavItems;
});

// Import para computed, necessário para reatividade
import { computed } from 'vue';

// Função auxiliar para ícones dinâmicos (se o 'icon' do dashboard for um nome de string)
// Você precisaria de um mapa de strings para componentes de ícone
// const dynamicIconComponent = (iconName: string) => {
//     const icons = {
//         'BookOpen': BookOpen,
//         'Folder': Folder,
//         // ... adicione outros ícones que você usa
//     };
//     return icons[iconName] || BookOpen; // Retorna BookOpen como fallback
// };

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('home')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain title="Inicial" :items="staticMainNavItems" />

            <NavMain v-if="user.is_super_admin" title="Administração" :items="superAdminNavItems" />

            <NavMainGroup v-if="dynamicNavGroups.length > 0" title="Instituições" :items="dynamicNavGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>