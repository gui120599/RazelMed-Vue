<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type MainNavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import { ChevronRight, LucideIcon } from 'lucide-vue-next';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';
import SidebarMenuSub from './ui/sidebar/SidebarMenuSub.vue';
import SidebarMenuSubItem from './ui/sidebar/SidebarMenuSubItem.vue';
import SidebarMenuSubButton from './ui/sidebar/SidebarMenuSubButton.vue';
import Collapsible from './ui/collapsible/Collapsible.vue';

defineProps<{
    items: MainNavGroup[];
    title?: string;
}>();

const page = usePage();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>{{ title }}</SidebarGroupLabel>
        <SidebarMenu>
            <Collapsible v-for="item in items" :key="item.title" as-child :default-open="item.isActive"
                class="group/collapsible">
                <SidebarMenuItem>
                    <CollapsibleTrigger as-child>
                        <SidebarMenuButton :tooltip="item.title">
                            <component :is="item.icon" v-if="item.icon" />
                            <span>{{ item.title }}</span>
                            <ChevronRight
                                class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                        </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                        <SidebarMenuSub>
                            <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.title">
                                <SidebarMenuSubButton as-child>
                                    <Link :href="subItem.url">
                                        <span>{{ subItem.title }}</span>
                                    </Link>
                                </SidebarMenuSubButton>
                            </SidebarMenuSubItem>
                        </SidebarMenuSub>
                    </CollapsibleContent>
                </SidebarMenuItem>
            </Collapsible>
        </SidebarMenu>
    </SidebarGroup>
</template>
