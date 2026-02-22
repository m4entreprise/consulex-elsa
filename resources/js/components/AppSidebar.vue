<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ClipboardList, Globe, LayoutGrid, UtensilsCrossed, UserCheck } from 'lucide-vue-next';
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
        title: 'Site web',
        icon: Globe,
        items: [
            {
                title: 'Général',
                href: '/admin/settings',
            },
            {
                title: 'Modalités pratiques',
                href: '/admin/practical-modalities',
            },
            {
                title: 'After movies',
                href: '/admin/after-movies',
            },
            {
                title: 'Jury',
                href: '/admin/jury-members',
            },
            {
                title: 'Partenaires',
                href: '/admin/partners',
            },
        ],
    },
    {
        title: 'Nourriture',
        href: '/admin/food-options',
        icon: UtensilsCrossed,
    },
    {
        title: 'Inscriptions',
        icon: UserCheck,
        items: [
            {
                title: 'Spectateurs',
                href: '/admin/registrations/spectators',
            },
            {
                title: 'Candidats',
                href: '/admin/registrations/candidates',
            },
        ],
    },
    {
        title: 'Récap',
        href: '/admin/recap',
        icon: ClipboardList,
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
