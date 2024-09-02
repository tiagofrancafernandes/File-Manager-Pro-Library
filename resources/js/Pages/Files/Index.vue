<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PdfFile from '@/Pages/Files/TypeIcons/PdfFile.vue'
import Directory from '@/Pages/Files/TypeIcons/Directory.vue'
import ItemCard from '@/Pages/Files/ItemCard.vue'
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    items: {
        type: Array,
    }
});

let modalOpen = ref(false);

const toggleModal = () => {
    modalOpen.value = !modalOpen.value
};

const preparedItems = computed(() => {
    let items = props.items || [];

    return !Array.isArray(items) ? items : items.map(item => {
        return {
            ...item,
            class: {
                'bg-red-400': item.type === 'dir' && item.color === 'red',
                'bg-blue-400': item.type === 'dir' && item.color === 'blue',
                'bg-green-400': item.type === 'dir' && item.color === 'green',
            }
        }
    })
})

const dirsOnly = computed(() => {
    return preparedItems.value.filter(item => item.type === 'dir');
});

const notDirsOnly = computed(() => {
    return preparedItems.value.filter(item => item.type !== 'dir');
});

console.log('items', props.items);

/*
hashid
base_path
owner_username
type
color
name
updated_at
updated_at_humans_label
mime_type
size
size_humans_label
*/
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Files
            </h2>
        </template>

        <div class="">
            <div class="w-full p-2">
                <!-- Breadcrumb -->
                <nav class="flex px-5 py-3 text-gray-700 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                        <li class="inline-flex items-center">
                        <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Home
                        </a>
                        </li>
                        <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Templates</a>
                        </div>
                        </li>
                        <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Flowbite</span>
                        </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="py-10 px-2 md:px-5">
                <div class="mb-6 grid grid-cols-4 !gap-5 max-md:grid-cols-1">
                    <template
                        v-for="(item, itemIndex) in dirsOnly"
                        :key="itemIndex"
                    >
                        <ItemCard :item="item" />
                    </template>
                </div>
            </div>

            <div class="py-10 px-2 md:px-5">
                <div class="mb-6 grid grid-cols-4 !gap-5 max-md:grid-cols-1 absolute">
                    <template
                        v-for="(item, itemIndex) in notDirsOnly"
                        :key="itemIndex"
                    >
                        <ItemCard :item="item" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
