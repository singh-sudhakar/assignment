<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3>Patient Stats</h3>
                    </div>
                    <div class="py-6 flex justify-between">
                        <div class="w-2/4 px-2">
                            <PieChart />
                        </div>
                        <div class="w-3/4 px-2">
                            <BarChart />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
