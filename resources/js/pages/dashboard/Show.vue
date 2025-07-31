<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    dashboard: {
        type: Object,
        required: true,
    },
});

const breadcrumbs = [
    { title: 'Home', href: '/home' },
    { title: props.dashboard.institution.reduced_name, href: '/home' },
    { title: props.dashboard.name, href: route('dashboards.show', props.dashboard.id) },
];

// 🔒 Regras de bloqueio
onMounted(() => {
    const blockContextMenu = e => e.preventDefault();
    const blockKeyCombos = e => {
        const key = e.key.toUpperCase();
        if (
            key === 'F12' ||
            (e.ctrlKey && e.shiftKey && ['I', 'C', 'J'].includes(key)) ||
            (e.ctrlKey && key === 'U')
        ) {
            e.preventDefault();
        }
    };
    const blockClipboard = e => e.preventDefault();

    document.addEventListener('contextmenu', blockContextMenu);
    document.addEventListener('keydown', blockKeyCombos);
    ['copy', 'cut', 'paste'].forEach(evt =>
        document.addEventListener(evt, blockClipboard)
    );

    onBeforeUnmount(() => {
        document.removeEventListener('contextmenu', blockContextMenu);
        document.removeEventListener('keydown', blockKeyCombos);
        ['copy', 'cut', 'paste'].forEach(evt =>
            document.removeEventListener(evt, blockClipboard)
        );
    });
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head :title="`Dashboard: ${dashboard.name}`" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-2 overflow-x-auto">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow" @contextmenu.prevent>
                <iframe :src="dashboard.iframe_link" frameborder="0" class="w-full h-screen"
                    sandbox="allow-scripts allow-same-origin"></iframe>
            </div>
        </div>
    </AppLayout>
</template>