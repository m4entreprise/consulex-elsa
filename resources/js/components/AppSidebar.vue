<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Handshake, LayoutGrid, SlidersHorizontal, UtensilsCrossed, Users } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Paramètres',
        href: '/admin/settings',
        icon: SlidersHorizontal,
    },
    {
        title: 'Partenaires',
        href: '/admin/partners',
        icon: Handshake,
    },
    {
        title: 'Nourriture',
        href: '/admin/food-options',
        icon: UtensilsCrossed,
    },
    {
        title: 'Spectateurs',
        href: '/admin/registrations/spectators',
        icon: Users,
    },
    {
        title: 'Candidats',
        href: '/admin/registrations/candidates',
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
