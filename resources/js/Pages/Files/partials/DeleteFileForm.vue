<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const props = defineProps({
    file: {
        type: Object,
        required: true,
    },
});

const confirmingFileDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmFileDeletion = () => {
    confirmingFileDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteFile = () => {
    form.delete(route('files.destroy', {
        hashedid: props?.file?.hashedid,
    }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingFileDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <template v-if="$slots.trigger">
            <span @click="confirmFileDeletion">
                <slot name="trigger"/>
            </span>
        </template>
        <template v-else>
            <DangerButton class="w-full h-5 py-3" @click="confirmFileDeletion">Delete file</DangerButton>
        </template>

        <Modal :show="confirmingFileDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete this file?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your file is deleted, all of its resources and data will be permanently deleted. Please
                    enter your password to confirm you would like to permanently delete your file.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteFile"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteFile"
                    >
                        Delete file
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
