<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import AppLogoPng from '@/components/AppLogoPng.vue';
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

function filteredDashboards(institution) {
    return props.dashboards.filter(d => d.institution_id === institution.id);
}

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
                        <AppLogoPng class="dark:bg-white dark:rounded-4xl p-4" />
                    </div>
                </div>
                <div
                    class="flex flex-col items-center justify-center p-2 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex flex-col items-start justify-center">
                        <h1 class="text-4xl font-semibold">Olá,</h1>
                        <span class="text-3xl">{{ user.name }}</span>
                    </div>

                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <p class="m-2 text-2xl text-center">Instituições e seus dashboards disponíveis</p>
                <Accordion type="multiple" class="w-full p-4" collapsible>
                    <AccordionItem v-for="institution in props.institutions" :key="institution.name"
                        :value="institution.name">
                        <AccordionTrigger class="cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <img v-if="institution.profile_photo_path"
                                    :src="`/storage/${institution.profile_photo_path}`"
                                    :alt="`${institution.name || 'Instituição'}`"
                                    class="w-10 h-10 rounded-full object-cover" />
                                <div v-else
                                    class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                    No Photo</div>
                                <span>{{ institution.name }}</span>
                            </div>

                        </AccordionTrigger>
                        <AccordionContent>
                            <div v-if="filteredDashboards(institution).length > 0">
                                <div v-for="dashboard in filteredDashboards(institution)" :key="dashboard.id"
                                    class="p-2 pl-6">
                                    <Link :href="route('dashboards.show', dashboard.id)"
                                        class="text-blue-600 hover:underline">
                                    {{ dashboard.name }}
                                    </Link>
                                </div>
                            </div>
                            <div v-else class="p-4 text-center text-muted-foreground">
                                Nenhum dashboard encontrado para esta instituição.
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </AppLayout>
</template>