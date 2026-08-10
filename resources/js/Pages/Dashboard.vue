<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Link} from "@inertiajs/vue3";
import UserLayout from "@/Layouts/UserLayout.vue";
</script>

<template>
    <Head title="Dashboard" />

    <UserLayout>
        <div class="py-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                    <Link :href="route('admin.index')">Админка</Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
