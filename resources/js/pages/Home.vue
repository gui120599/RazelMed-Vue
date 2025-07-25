<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Home',
        href: '/home',
    },
];

</script>

<template>

    <Head title="Home" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="h-full flex justify-center p-8 absolute inset-0">
                        <AppLogoIcon class="" />
                    </div>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-2 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex flex-col items-start justify-center">
                        <h1 class="text-2xl font-semibold">Olá,</h1>
                        <span class="">{{ user.name }}</span>
                    </div>

                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
