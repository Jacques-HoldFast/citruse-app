<script setup>
import { ref, computed, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useApi } from '@/Composables/useApi';

const showingNavigationDropdown = ref(false);
const page = usePage();
const { api } = useApi();

// User data from API (like your Dashboard)
const userData = ref(null);

// Get user data - try page props first, then API
const user = computed(() => {
    // First try API data (your current working method)
    if (userData.value?.user) {
        return userData.value.user;
    }
    
    // Fallback to page props (standard Inertia way)
    if (page.props.auth?.user) {
        return page.props.auth.user;
    }
    
    return {};
});

const userRole = computed(() => user.value?.role || '');
const permissions = computed(() => user.value?.permissions || {});

// Helper functions for role checking (same as your Dashboard)
const canManageSuppliers = computed(() => {
    return user.value?.permissions?.can_manage_suppliers || false;
});

const canCreatePurchaseOrders = computed(() => {
    return user.value?.permissions?.can_create_purchase_orders || false;
});

const isSystemAdmin = computed(() => {
    return user.value?.permissions?.is_system_admin || false;
});

const isPurchasingManager = computed(() => userRole.value === 'purchasing_manager');
const isFieldSalesAssociate = computed(() => userRole.value === 'field_sales_associate');

// Role display name
const roleDisplayName = computed(() => {
    const roleNames = {
        'system_administrator': 'System Admin',
        'purchasing_manager': 'Purchasing Manager', 
        'field_sales_associate': 'Field Sales'
    };
    return roleNames[userRole.value] || 'User';
});

// Role color for badge
const roleColor = computed(() => {
    const colors = {
        'system_administrator': 'bg-red-100 text-red-800',
        'purchasing_manager': 'bg-blue-100 text-blue-800',
        'field_sales_associate': 'bg-green-100 text-green-800'
    };
    return colors[userRole.value] || 'bg-gray-100 text-gray-800';
});

// Load user data (same pattern as your Dashboard)
const loadUserData = async () => {
    try {
        const response = await api.getUser();
        userData.value = response;
        console.log('Layout loaded user data:', userData.value);
    } catch (err) {
        console.error('Error loading user data in layout:', err);
    }
};

// Load user data on mount
onMounted(() => {
    loadUserData();
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="flex items-center space-x-2">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                                    <span class="text-xl font-bold text-gray-800">Citruse</span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <!-- Dashboard - Always visible -->
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>

                                <!-- Products - All users can view -->
                                <NavLink :href="route('products.index')" :active="route().current('products.*')">
                                    Products
                                </NavLink>

                                <!-- Suppliers & Distributors - System Admin & Purchasing Manager -->
                                <template v-if="canManageSuppliers">
                                    <NavLink :href="route('suppliers.index')" :active="route().current('suppliers.*')">
                                        Suppliers
                                    </NavLink>
                                    <NavLink :href="route('distributors.index')" :active="route().current('distributors.*')">
                                        Distributors
                                    </NavLink>
                                </template>

                                <!-- Purchase Orders - All users (filtered by role in backend) -->
                                <NavLink :href="route('purchase-orders.index')" :active="route().current('purchase-orders.*')">
                                    Orders
                                </NavLink>

                                <!-- Pipeline Forecast -->
                                <NavLink :href="route('purchase-orders.forecast')" :active="route().current('purchase-orders.forecast')">
                                    Forecast
                                </NavLink>

                                <!-- Users - System Admin only -->
                                <NavLink v-if="isSystemAdmin" :href="route('users.index')" :active="route().current('users.*')">
                                    Users
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center space-x-4">
                            <!-- Role Badge -->
                            <div v-if="user.name" class="text-sm">
                                <span :class="roleColor" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ roleDisplayName }}
                                </span>
                            </div>

                            <!-- Quick Actions Dropdown -->
                            <div v-if="user.name" class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            Quick Actions
                                            <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <!-- Create Purchase Order -->
                                        <DropdownLink v-if="canCreatePurchaseOrders" :href="route('purchase-orders.create')">
                                            📝 Create Purchase Order
                                        </DropdownLink>
                                        
                                        <!-- Add Supplier -->
                                        <DropdownLink v-if="canManageSuppliers" :href="route('suppliers.create')">
                                            🏢 Add Supplier
                                        </DropdownLink>
                                        
                                        <!-- Add Distributor -->
                                        <DropdownLink v-if="canManageSuppliers" :href="route('distributors.create')">
                                            🌍 Add Distributor
                                        </DropdownLink>
                                        
                                        <!-- View Products -->
                                        <DropdownLink :href="route('products.index')">
                                            🍊 View Products
                                        </DropdownLink>
                                        
                                        <!-- Pipeline Forecast -->
                                        <DropdownLink :href="route('purchase-orders.forecast')">
                                            📊 Pipeline Forecast
                                        </DropdownLink>
                                        
                                        <!-- Divider -->
                                        <hr class="border-gray-200 my-1">
                                        
                                        <!-- Create User (System Admin only) -->
                                        <DropdownLink v-if="isSystemAdmin" :href="route('users.create')">
                                            👥 Create User
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- User Settings Dropdown -->
                            <div v-if="user.name" class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                                {{ user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- User Info -->
                                        <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-200">
                                            {{ user.email }}<br>
                                            <span :class="roleColor" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium mt-1">
                                                {{ roleDisplayName }}
                                            </span>
                                        </div>
                                        
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Loading indicator when user data not loaded -->
                            <div v-else class="flex items-center text-sm text-gray-500">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-500 mr-2"></div>
                                Loading...
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                          stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Dashboard -->
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>

                        <!-- Products -->
                        <ResponsiveNavLink :href="route('products.index')" :active="route().current('products.*')">
                            Products
                        </ResponsiveNavLink>

                        <!-- Suppliers & Distributors -->
                        <template v-if="canManageSuppliers">
                            <ResponsiveNavLink :href="route('suppliers.index')" :active="route().current('suppliers.*')">
                                Suppliers
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('distributors.index')" :active="route().current('distributors.*')">
                                Distributors
                            </ResponsiveNavLink>
                        </template>

                        <!-- Purchase Orders -->
                        <ResponsiveNavLink :href="route('purchase-orders.index')" :active="route().current('purchase-orders.*')">
                            Orders
                        </ResponsiveNavLink>

                        <!-- Pipeline Forecast -->
                        <ResponsiveNavLink :href="route('purchase-orders.forecast')" :active="route().current('purchase-orders.forecast')">
                            Forecast
                        </ResponsiveNavLink>

                        <!-- Users (System Admin only) -->
                        <ResponsiveNavLink v-if="isSystemAdmin" :href="route('users.index')" :active="route().current('users.*')">
                            Users
                        </ResponsiveNavLink>

                        <!-- Quick Actions Section -->
                        <div v-if="user.name" class="pt-2 pb-1 border-t border-gray-200">
                            <div class="px-4 text-xs font-medium text-gray-500 uppercase">Quick Actions</div>
                            
                            <ResponsiveNavLink v-if="canCreatePurchaseOrders" :href="route('purchase-orders.create')">
                                📝 Create Purchase Order
                            </ResponsiveNavLink>
                            
                            <ResponsiveNavLink v-if="canManageSuppliers" :href="route('suppliers.create')">
                                🏢 Add Supplier
                            </ResponsiveNavLink>
                            
                            <ResponsiveNavLink v-if="canManageSuppliers" :href="route('distributors.create')">
                                🌍 Add Distributor
                            </ResponsiveNavLink>
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div v-if="user.name" class="border-t border-gray-200 pb-1 pt-4">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                            <div class="mt-1">
                                <span :class="roleColor" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                    {{ roleDisplayName }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>