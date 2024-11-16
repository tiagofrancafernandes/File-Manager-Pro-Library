<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite';
import { onMounted } from 'vue';
import ToggleDarkMode from '@/Layouts/parts/ToggleDarkMode.vue';
onMounted(() => {
    initFlowbite();
})
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="flex md:flex-col w-full md:w-3/12 justify-content-center items-center gap-y-3">
            <Link href="/"  class="flex w-full justify-content-center items-center">
                <ApplicationLogo class="w-20 h-20 mx-auto fill-current text-gray-500" />
            </Link>
            <ToggleDarkMode/>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
