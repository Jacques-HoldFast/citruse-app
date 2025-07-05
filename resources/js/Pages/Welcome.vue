<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome to Citruse" />

    <div class="bg-gray-50 text-black/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <div class="flex items-center space-x-3">
                            <div class="text-4xl">🍊</div>
                            <div class="text-3xl font-bold text-gray-900">Citruse</div>
                        </div>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20]"
                        >
                            Register
                        </Link>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                        <!-- Welcome Section -->
                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-lg ring-1 ring-gray-200 transition duration-300 hover:shadow-xl">
                            <div class="relative flex items-center gap-6 lg:items-end">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-orange-100 sm:size-16">
                                    <div class="text-2xl">🍊</div>
                                </div>
                                <div class="pt-3 sm:pt-5 lg:pt-0">
                                    <h2 class="text-xl font-semibold text-black">
                                        Citruse Fruit Management
                                    </h2>
                                    <p class="mt-4 text-sm/relaxed text-gray-600">
                                        Your comprehensive platform for managing citrus fruit orders, suppliers, and distributors across South Africa, Mauritius, Mozambique, and Tanzania.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Features Section -->
                        <div class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-lg ring-1 ring-gray-200 transition duration-300 hover:shadow-xl">
                            <div class="relative flex items-center gap-6 lg:items-end">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:size-16">
                                    <div class="text-2xl">📋</div>
                                </div>
                                <div class="pt-3 sm:pt-5 lg:pt-0">
                                    <h2 class="text-xl font-semibold text-black">
                                        Key Features
                                    </h2>
                                </div>
                            </div>

                            <div class="space-y-4 text-sm/relaxed">
                                <div class="flex items-start space-x-3">
                                    <div class="text-blue-500 mt-0.5">🏢</div>
                                    <div>
                                        <p class="font-medium">Supplier & Distributor Management</p>
                                        <p class="text-gray-600">Manage contact information and track performance</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="text-green-500 mt-0.5">📦</div>
                                    <div>
                                        <p class="font-medium">Purchase Order System</p>
                                        <p class="text-gray-600">POD orders from distributors and POS orders to suppliers</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="text-orange-500 mt-0.5">🍊</div>
                                    <div>
                                        <p class="font-medium">Product Catalog</p>
                                        <p class="text-gray-600">Oranges, lemons, limes and grapefruit with pricing</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="text-purple-500 mt-0.5">📊</div>
                                    <div>
                                        <p class="font-medium">Pipeline Forecasting</p>
                                        <p class="text-gray-600">Track deliveries and identify supply shortages</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Get Started Section -->
                        <div class="lg:col-span-2">
                            <div class="flex flex-col items-center gap-6 overflow-hidden rounded-lg bg-gradient-to-r from-orange-500 to-red-500 p-8 shadow-lg text-white">
                                <div class="text-center">
                                    <h2 class="text-2xl font-bold mb-4">
                                        Ready to Get Started?
                                    </h2>
                                    <p class="text-lg opacity-90 mb-6">
                                        Sign in to access your dashboard and manage your citrus fruit operations.
                                    </p>
                                    <div class="flex justify-center space-x-4">
                                        <Link
                                            v-if="canLogin"
                                            :href="route('login')"
                                            class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-orange-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
                                        >
                                            Sign In
                                        </Link>
                                        <Link
                                            v-if="canRegister"
                                            :href="route('register')"
                                            class="inline-flex items-center px-6 py-3 border border-white text-base font-medium rounded-md text-white bg-transparent hover:bg-white hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors"
                                        >
                                            Register
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-gray-500">
                    <div class="mb-4">
                        <p class="font-medium">Citruse Fruit Management System</p>
                        <p>Connecting growers, suppliers, and distributors across Africa</p>
                    </div>
                    <p>Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
                </footer>
            </div>
        </div>
    </div>
</template>