<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Building2, ChevronDown, Folder, Headset, LayoutDashboard, LayoutGrid, Package, User } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { useSessionStorage } from '@vueuse/core';
import SidebarGroup from './ui/sidebar/SidebarGroup.vue';
import SidebarGroupLabel from './ui/sidebar/SidebarGroupLabel.vue';
import SidebarGroupContent from './ui/sidebar/SidebarGroupContent.vue';
import Collapsible from './ui/collapsible/Collapsible.vue';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';
import Accordion from '@/components/ui/accordion/Accordion.vue';
import AccordionItem from '@/components/ui/accordion/AccordionItem.vue';
import AccordionTrigger from '@/components/ui/accordion/AccordionTrigger.vue';
import AccordionContent from '@/components/ui/accordion/AccordionContent.vue';

const props = defineProps({
    dashboards: {
        type: Array as () => DashboardItem[],
        required: true,
    },
    institutions: {
        type: Array as () => InstitutionItem[],
        required: true,
    },
});

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
    reduced_name: string;
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
const adminNavItems: NavItem[] = [
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
        title: 'Suporte',
        href: '/home',
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
            <SidebarGroup>
                <SidebarGroupLabel>
                    <span>Dashboards</span>
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <div v-for="institution in institutions" :key="institution.id">
                        <Collapsible class="group/collapsible">
                            <SidebarGroup>
                                <SidebarGroupLabel asChild>
                                    <CollapsibleTrigger class="w-full text-left">
                                        <div class="flex items-center space-x-2">
                                            <img v-if="institution.profile_photo_path"
                                                :src="`/storage/${institution.profile_photo_path}`"
                                                :alt="`${institution.name || 'Instituição'}`"
                                                class="w-6 h-6 rounded-full object-cover" />
                                            <div v-else
                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                                No Photo
                                            </div>
                                            <span>{{ institution.reduced_name }}</span>
                                        </div>
                                        <ChevronDown
                                            class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
                                    </CollapsibleTrigger>
                                </SidebarGroupLabel>
                                <CollapsibleContent>
                                    <SidebarGroupContent class="border-l ml-2 pl-2">
                                        <div
                                            v-if="dashboards.filter(d => d.institution_id === institution.id).length > 0">
                                            <SidebarMenuItem
                                                v-for="dashboard in dashboards.filter(d => d.institution_id === institution.id)"
                                                :key="dashboard.id" asChild>
                                                <Link :href="route('dashboards.show', dashboard.id)">
                                                {{ dashboard.name }}
                                                </Link>
                                            </SidebarMenuItem>
                                        </div>
                                        <div v-else>
                                            <SidebarMenuItem>
                                                Nenhum dashboard
                                            </SidebarMenuItem>
                                        </div>
                                    </SidebarGroupContent>
                                </CollapsibleContent>
                            </SidebarGroup>
                        </Collapsible>
                    </div>
                </SidebarGroupContent>
            </SidebarGroup>
            
            <NavMain v-if="user.is_super_admin" title="Administração" :items="adminNavItems" />

        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
