<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import { ChevronDown } from 'lucide-vue-next';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';
import SidebarGroupContent from './ui/sidebar/SidebarGroupContent.vue';



const page = usePage();
</script>

<template>
  <Collapsible defaultOpen class="group/collapsible">
    <SidebarGroup>
      <SidebarGroupLabel asChild>
        <CollapsibleTrigger>
          Help
          <ChevronDown class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180" />
        </CollapsibleTrigger>
      </SidebarGroupLabel>
      <CollapsibleContent>
        <SidebarGroupContent />
      </CollapsibleContent>
    </SidebarGroup>
  </Collapsible>
</template>
