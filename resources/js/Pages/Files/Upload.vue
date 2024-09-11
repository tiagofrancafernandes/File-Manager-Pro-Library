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
        <div class="bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="text-gray-700 divide-y divide-dashed">
                <div class="w-full p-2">
                    <!-- Breadcrumb -->
                    <nav class="flex px-5 py-3" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                            <template
                                v-for="(item, itemIndex) in breadcrumb"
                                :key="itemIndex"
                            >
                                <li v-if="itemIndex === 0"  class="inline-flex items-center">
                                    <Link
                                        :href="route('files.index')"
                                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-800 hover:underline dark:text-gray-400 dark:hover:text-white"
                                    >
                                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                                        </svg>
                                        Home
                                    </Link>
                                </li>
                                <template v-else>
                                    <li v-if="itemIndex < breadcrumb?.length -1">
                                        <div class="flex items-center">
                                            <svg class="rtl:rotate-180 block w-3 h-3 mx-1 text-gray-400 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                            </svg>
                                            <Link
                                                :href="route('files.index', {folder: item.folder})"
                                                class="ms-1 text-sm font-medium text-gray-700 hover:text-gray-800 hover:underline md:ms-2 dark:text-gray-400 dark:hover:text-white"
                                            >{{ item.label }}</Link>
                                        </div>
                                    </li>
                                    <li
                                        v-else
                                        aria-current="page"
                                    >
                                        <div class="flex items-center">
                                            <svg class="rtl:rotate-180  w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                            </svg>
                                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ item.label }}</span>
                                        </div>
                                    </li>
                                </template>
                            </template>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="flex flex-row w-full py-24">
                <div class="w-6/12 mx-auto">
                    <form @submit.prevent="submit">
                        <div class="w-full my-3 px-3">
                            <div class="grid grid-cols-4 gap-3">
                                <div class="w-full col-span-4 text-gray-50 bg-gray-600 rounded-md">
                                    <input
                                        type="file"
                                        class="w-full rounded-md"
                                        @change="handleFileChange"
                                        multiple
                                        accept=".pdf"
                                        required
                                    />
                                </div>

                                <div class="w-full col-span-4"></div>
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <PrimaryButton
                                    :disabled="form.processing || !(form.files_to_upload ?? [])?.length"
                                >Upload</PrimaryButton>
                            </div>
                        </div>
                    </form>

                    <div class="grid grid-cols-4 gap-3 px-3 mt-4">
                        <div class="w-full col-span-4">
                            <ul class="w-full flex gap-y-3 flex-col">
                                <template
                                    v-for="(file, fileIndex) in (form.files_to_upload || [])"
                                    :key="fileIndex"
                                >
                                    <li
                                        :class="[
                                            'flex align-conter w-full justify-around rounded-md shadow',
                                            'bg-gray-200 hover:bg-gray-300/70 dark:bg-gray-700 dark:hover:bg-gray-500/50',
                                            'px-2 dark:text-gray-400',
                                        ]"
                                    >
                                        <div class="w-full grid grid-cols-12 gap-x-2 justify-stretch">
                                            <div class="flex items-center justify-center">
                                                <template v-if="['application/pdf', 'text/plain'].includes(file.type)">
                                                    <svg v-if="file.type === 'application/pdf'" class="size-6" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"></path>
                                                        <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.245.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"></path>
                                                    </svg>
                                                    <template  v-if="file.type === 'text/plain'">
                                                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z"></path>
                                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1"></path>
                                                        </svg>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <svg class="size-6" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"></path>
                                                    </svg>
                                                </template>
                                            </div>

                                            <div class="col-span-10 text-gray-600 flex flex-col">
                                                <span
                                                    :class="[
                                                        'col-span-11 text-sm font-normal gap-x-2 flex',
                                                        'text-gray-600/70 dark:text-gray-400'
                                                    ]"
                                                >{{ file.name }}</span>
                                                <p
                                                    :class="[
                                                        'col-span-11 light text-[0.7rem] italic gap-x-2 flex',
                                                        'text-gray-600/70 dark:text-gray-400/70'
                                                    ]"
                                                >
                                                    <span>{{ file.type }}</span>
                                                    <span>{{ formatSize(file.size) }}</span>
                                                </p>
                                            </div>

                                            <div class="col-span-1 justify-end items-center text-end flex py-1">
                                                <button
                                                    type="button"
                                                    v-on:click="removeItem(file, fileIndex)"
                                                    class="font-semibold cursor-pointer px-3 rounded-md"
                                                    :class="[
                                                        'bg-gray-400/50 text-gray-500',
                                                        'dark:bg-gray-400/30 dark:text-gray-700',
                                                    ]"
                                                >x</button>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PdfFile from '@/Pages/Files/TypeIcons/PdfFile.vue';
import Directory from '@/Pages/Files/TypeIcons/Directory.vue';
import ItemCard from '@/Pages/Files/ItemCard.vue';
// import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { formatSize } from '@/Helpers/formaters';

const form = useForm({
    files_to_upload: null
})

const imagePreviewUrl = ref(null)

function handleFileChange (event) {
    form.files_to_upload = event.target.files

    if (form.files_to_upload?.length) {
        console.log('form.files_to_upload', form.files_to_upload);
        // imagePreviewUrl.value = URL.createObjectURL(form.files_to_upload?.item(0))
    }
}

function submit () {
    form.post(route('files.upload_process'))
}

function removeItem(item, itemIndex) {
    if (!form.files_to_upload?.length) {
        return;
    }

    form.files_to_upload = [...form.files_to_upload].filter((f, index) => index !== itemIndex);
}

// let file = {
//     lastModified: 1724451177421,
//     name: "pdf_sample_2-1.pdf",
//     size: 10527,
//     type: "application/pdf",
// };
</script>
