<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Building2, Folder, Headset, LayoutDashboard, LayoutGrid, Package, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

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
                        <Link :href="route('dashboard')">
                        <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain title="Menu" :items="mainNavItems" />
            <NavMain title="Administração" :items="adminNavItems" />
            
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
