<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
});

const title = {
    503: 'Serviço Indisponível',
    500: 'Erro no Servidor',
    403: 'Proibido',
    404: 'Página Não Encontrada',
}[props.status];

const description = {
    503: 'Estamos fazendo uma manutenção. Por favor, tente novamente em breve.',
    500: 'Ocorreu um erro no servidor. Por favor, tente novamente.',
    403: 'Você não tem permissão para acessar esta página.',
    404: 'Desculpe, a página que você está procurando não foi encontrada.',
}[props.status];
</script>

<template>
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="flex flex-col items-center justify-center h-full text-center">
                <h1 class="text-6xl font-bold text-destructive">{{ status }}</h1>
                <h2 class="mt-4 text-3xl font-semibold text-gray-800 dark:text-gray-200">{{ title }}</h2>
                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">{{ description }}</p>
                <Link :href="route('home')" class="mt-8 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                    Voltar para o Início
                </Link>
            </div>
        </div>
    </AppLayout>
</template>