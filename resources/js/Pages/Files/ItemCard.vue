<script setup lang="js">
import { router } from '@inertiajs/vue3';
import { useSlots, useAttrs, ref } from 'vue';
import PdfFile from '@/Pages/Files/TypeIcons/PdfFile.vue';
import Directory from '@/Pages/Files/TypeIcons/Directory.vue';
import { FwbDropdown, FwbListGroup, FwbListGroupItem } from 'flowbite-vue';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    navigateToItem: {
        type: Boolean,
        default: true,
    }
});

const slots = useSlots();
const attrs = useAttrs();

let showActions = ref(false);
let itemData = ref(props.item);

const toggleShowActions = (event) => {
    console.log('toggleShowActions');
    showActions.value = !showActions.value;
}

const hideShowActions = (event) => {
    console.log('hideShowActions');
    showActions.value = false;
}

const toggleFavorite = (item) => {
    console.log('item', item);
    itemData.value.favorite = !itemData.value.favorite;
}

const onClickOutside = (event) => {
    console.log('Clicked outside. Event: ', event);
}

const openRenderedPdf = (file) => {
    file = file && typeof file === 'object' ? file : null;

    if (!file || Array.isArray(file) || file.typeName !== 'FILE' || file.mime_type !== 'application/pdf') {
        return null;
    }

    router.visit(route('files.render_pdf_protected', file?.hashedid), {
        method: 'get',
        data: {
            hashedid: file?.hashedid,
        }
    });
}
</script>

<template>
    <div class="flex flex-col">
        <div v-if="false" class="w-full">
            <fwb-dropdown>
                <template #trigger>
                    <span>Custom Trigger Element</span>
                </template>
                <fwb-list-group>
                    <fwb-list-group-item> Profile </fwb-list-group-item>
                    <fwb-list-group-item> Settings </fwb-list-group-item>
                    <fwb-list-group-item> Messages </fwb-list-group-item>
                    <fwb-list-group-item> Download </fwb-list-group-item>
                </fwb-list-group>
            </fwb-dropdown>
        </div>

        <div class="w-full">
            <div
                aclass="lqd-card w-full group/card lqd-card-outline border border-card-border lqd-card-roundness-default rounded-xl lqd-docs-folder relative__z-20 px-5 py-2.5 bg-folder-background text-folder-foreground transition-all hover:scale-[1.022] hover:shadow-md"
                :class="[
                    'group/card cursor-pointer',
                    'bg-white dark:bg-gray-800',
                    'text-gray-800 dark:text-gray-50',
                    'w-full max-w-sm border border-gray-200 rounded-lg dark:border-gray-700',
                    'bg-folder-background text-folder-foreground transition-all --hover:shadow-md',
                    'border px-1.5 py-1',
                    'hover:shadow-md dark:hover:shadow-md hover:shadow-gray-400/10 dark:hover:shadow-gray-200/10'
                ]"
                v-click-outside="hideShowActions"
            >
                <div
                    class="lqd-card-body relative only:grow lqd-card-size-none flex items-center justify-between gap-5"
                >
                    <div class="grid grid-cols-6 gap-x-1 w-full">
                        <div class="col-span-1">
                            <template v-if="!slots.icon">
                                <PdfFile v-if="itemData.typeName === 'FILE'" />
                                <Directory v-if="itemData.typeName === 'DIR'" />
                            </template>
                            <template v-else>
                                <slot name="icon" />
                            </template>
                        </div>

                        <div class="col-span-4">
                            <div class="text-2xs">
                                <p class="m-0 font-medium">
                                    {{ itemData.label }}
                                </p>
                                <div class="flex gap-x-4">
                                    <small class="opacity-70">{{
                                        itemData.updated_at_humans_label
                                    }}</small>
                                    <small
                                        class="opacity-70"
                                        v-if="itemData.typeName === 'FILE'"
                                        >{{ itemData.size_humans_label }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <a
                            v-if="props.navigateToItem"
                            class="absolute inset-0"
                            href="#open-folder-xyz"
                        ></a>

                        <div class="col-span-1 relative">
                            <div
                                class="flex justify-center space-x-4 rtl:space-x-reverse me-3"
                            >
                                <button
                                    type="button"
                                    @click.prevent="toggleShowActions"
                                    class="inline-flex items-center py-2 px-1 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-0 focus:outline-none dark:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 4 15"
                                    >
                                        <path
                                            d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"
                                        ></path>
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div
                                    v-if="true"
                                    class="flex justify-center absolute z-50"
                                >
                                    <!-- Dropdown menu -->
                                    <div
                                        v-show="showActions"
                                        class="bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-800 dark:divide-gray-600 block absolute"
                                        :class="[
                                            'border border-gray-200 rounded-lg shadow dark:border-gray-700'
                                        ]"
                                        style="
                                            position: absolute;
                                            inset: 0px auto auto 0px;
                                            margin: 0px;
                                            transform: translate(1px, 45px);
                                            z-index: 7897987;
                                        "
                                        data-popper-placement="bottom"
                                        :aclass="{
                                            block: showActions,
                                            hidden: !showActions
                                        }"
                                    >
                                        <!-- <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                        <div>Bonnie Green</div>
                                        <div class="font-medium truncate">name@flowbite.com</div>
                                    </div> -->
                                        <ul
                                            class="relative py-1 text-sm text-gray-700 dark:text-gray-200"
                                        >
                                            <li>
                                                <a
                                                    href="#!"
                                                    class="flex items-center justify-start gap-2 px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke-width="1.5"
                                                        stroke="currentColor"
                                                        aria-hidden="true"
                                                        data-slot="icon"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"
                                                        ></path>
                                                    </svg>

                                                    Share
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="#!"
                                                    class="flex items-center justify-start gap-2 px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 16 16"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            clip-rule="evenodd"
                                                            d="M10 3h3v1h-1v9l-1 1H4l-1-1V4H2V3h3V2a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1zM9 2H6v1h3V2zM4 13h7V4H4v9zm2-8H5v7h1V5zm1 0h1v7H7V5zm2 0h1v7H9V5z"
                                                        ></path>
                                                    </svg>

                                                    Delete
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="#!"
                                                    class="flex items-center justify-start gap-2 px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                                >
                                                    <svg
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        width="16"
                                                        height="16"
                                                        fill="currentColor"
                                                        viewBox="0 0 16 16"
                                                        class="w-4 h-4"
                                                    >
                                                        <path
                                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"
                                                        ></path>
                                                        <path
                                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"
                                                        ></path>
                                                    </svg>

                                                    Info
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    href="#!"
                                                    class="flex items-center justify-start gap-2 px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                                >
                                                    <svg
                                                        class="w-4 h-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 256 256"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            d="M222,144v64a6,6,0,0,1-6,6H40a6,6,0,0,1-6-6V144a6,6,0,0,1,12,0v58H210V144a6,6,0,0,1,12,0Zm-98.24,4.24a6,6,0,0,0,8.48,0l40-40a6,6,0,0,0-8.48-8.48L134,129.51V32a6,6,0,0,0-12,0v97.51L92.24,99.76a6,6,0,0,0-8.48,8.48Z"
                                                        ></path>
                                                    </svg>

                                                    Download
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="py-1">
                                            <button
                                                type="button"
                                                v-on:click="
                                                    openRenderedPdf(itemData)
                                                "
                                                class="flex items-center justify-start gap-2 px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9.25 4.75H6.75C5.64543 4.75 4.75 5.64543 4.75 6.75V17.25C4.75 18.3546 5.64543 19.25 6.75 19.25H17.25C18.3546 19.25 19.25 18.3546 19.25 17.25V14.75"
                                                    ></path>
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19.25 9.25V4.75H14.75"
                                                    ></path>
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M19 5L11.75 12.25"
                                                    ></path>
                                                </svg>

                                                View rendered
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="absolute inline-flex items-center justify-center w-8 h-8 text-xs top-[-0.5rem] end-[-1.4rem]"
                                    @click.prevent.stopPropagation="
                                        toggleFavorite(item)
                                    "
                                >
                                    <svg
                                        v-if="itemData.favorite"
                                        class="w-8 h-8 text-orange-400"
                                        afill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M14 2v17l-4-4-4 4V2c0-.553.585-1.02 1-1h6c.689-.02 1 .447 1 1z"
                                        ></path>
                                    </svg>
                                    <svg
                                        v-if="!itemData.favorite"
                                        class="w-8 h-8 text-gray-50 dark:text-gray-500 hover:text-orange-400"
                                        fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aviewBox="0 0 20 20"
                                        afill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="0.5"
                                        stroke="gray"
                                        a_stroke="currentColor"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M14 2v17l-4-4-4 4V2c0-.553.585-1.02 1-1h6c.689-.02 1 .447 1 1z"
                                        ></path>
                                    </svg>
                                    <!-- <svg
                                    v-if="itemData.favorite"
                                    class="w-8 h-8 text-orange-400"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"></path>
                                </svg>
                                <svg
                                    v-if="!itemData.favorite"
                                    class="w-8 h-8"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"></path>
                                </svg> -->
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
