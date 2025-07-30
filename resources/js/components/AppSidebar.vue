<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Building2, Folder, Headset, LayoutDashboard, LayoutGrid, Package, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { useSessionStorage } from '@vueuse/core';

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
}

// Obtenha os props da página, incluindo o usuário autenticado
const page = usePage();
// Usar `as unknown as UserProps` é um bom truque, mas o ideal é que `page.props.auth.user`
// já seja tipado corretamente no seu projeto (ex: via um d.ts para props Inertia).
const user = page.props.auth.user as unknown as UserProps;

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/home',
        icon: LayoutDashboard,
    },

];
const adminNavItems:  NavItem[] = [
    {
        title: 'Instituições',
        href: route('institutions.index'),
        icon: Building2,
    },
    {
        title: 'Dashboards',
        href: route('dashboards.index'),
        icon: LayoutGrid,
    },
];

const footerNavItems: NavItem[] = [
    {
        title:'Suporte',
        href:'/home',
        icon: Headset,
    }
];
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
            <NavMain title="Menu" :items="mainNavItems" />
            <NavMain v-if="user.is_super_admin" title="Administração" :items="adminNavItems" />
            
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
