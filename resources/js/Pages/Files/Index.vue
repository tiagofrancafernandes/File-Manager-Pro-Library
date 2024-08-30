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
