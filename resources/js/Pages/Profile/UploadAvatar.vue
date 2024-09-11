<template>
    <form @submit.prevent="submit">
        <div class="w-full my-3">
            <img
                v-if="imagePreviewUrl"
                :src="imagePreviewUrl"
                class="max-w-xs max-h-xs my-4 size-48 rounded-full"
                alt="Image preview"
            />
            <input type="file" @change="handleFileChange" />
        </div>
        <div class="flex items-center gap-4 mt-6">
            <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref } from 'vue'

const form = useForm({
    profile_image: null
})

const imagePreviewUrl = ref(null)

function handleFileChange (event) {
    form.profile_image = event.target.files

    if (form.profile_image?.length) {
        console.log('form.profile_image', form.profile_image);
        imagePreviewUrl.value = URL.createObjectURL(form.profile_image?.item(0))
    }
}

function submit () {
    form.post(route('update_profile_image'))
}
</script>
