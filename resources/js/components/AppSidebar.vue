<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMasterData from '@/components/NavMasterData.vue';
import NavUser from '@/components/NavUser.vue';
import NavTransaction from '@/components/NavTransaction.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Bookmark, BookOpen, Folder, LayoutGrid, Ruler, Boxes, Warehouse, Layers, Calculator, Proportions, SendToBack, UsersRound } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const masterdataNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Categories',
        href: route('md.categories.index'),
        icon: Bookmark,
    },
    {
        title: 'Units',
        href: route('md.units.index'),
        icon: Ruler,
    },
    {
        title: 'Items',
        href: route('md.products.index'),
        icon: Boxes,
    },
    {
        title: 'Warehouses',
        href: route('md.warehouses.index'),
        icon: Warehouse,
    },
    {
        title: 'Customers',
        href: route('md.customers.index'),
        icon: UsersRound,
    },
];

const transNavItems: NavItem[] = [
    {
        title: 'Item Stock',
        href: route('trn.product_stocks.index'),
        icon: Layers,
    },
    {
        title: 'Stock Adjustment',
        href: route('trn.stock_adjustments.index'),
        icon: SendToBack,
    },
    {
        title: 'Point of Sale',
        href: route('trn.pos.index'),
        icon: Calculator,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
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
            <NavMasterData :items="masterdataNavItems" />
            <NavTransaction :items="transNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
