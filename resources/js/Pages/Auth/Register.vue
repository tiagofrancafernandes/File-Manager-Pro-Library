<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from "vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    username: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

function generateSlug(text) {
    text = typeof text === 'string' ? text : '';

    return text
        .toLowerCase() // Converte para minúsculas
        .trim() // Remove espaços em branco
        .replace(/[\s]+/g, '-') // Substitui espaços por hífens
        .replace(/[^\w\-]+/g, '') // Remove caracteres especiais
        .replace(/\-\-+/g, '-') // Remove hífens duplicados
        .replace(/^-+|-+$/g, ''); // Remove hífens do início e do fim
}

function validateRegexUsername() {
    const usernamePattern = /^[a-zA-Z0-9]{3,20}$/; // Aceita letras e números, entre 3 e 20 caracteres
    form.username = form?.username || '';
    form.username = typeof form.username === 'string' ? form.username : '';
    form.errors.username = typeof form.errors.username === 'string' ? form.errors.username : '';

    let notMatchError = 'The username must be between 3 and 20 characters and contain only letters and numbers.';

    if (!form.username.match(usernamePattern)) {
        form.errors.username = form.errors.username.includes(notMatchError) ? form.errors.username : [
            form.errors.username,
            notMatchError,
        ].filter(i => i).join('|');

        return;
    }

    if (form.errors.username.includes(notMatchError)) {
        form.errors.username = form.errors.username
            .replace(notMatchError + '|', '')
            .replace('|' + notMatchError, '')
            .replace(notMatchError, '');
    }

    // form.errors.username = '';
}

function validateUsernameValue() {
    form.username = form?.username || '';

    form.username = generateSlug(form?.username);
    validateRegexUsername();
}

const allowToSubmit = computed(() => {
    validateUsernameValue();

    if (
        !form?.name ||
        !form?.email ||
        !form?.username ||
        !form?.password ||
        !form?.password_confirmation ||
        (form?.password !== form?.password_confirmation)
    ) {
        return false;
    }

    console.log('form.errors?.username', form.errors?.username);

    if (form.errors?.username) {
        return false;
    }

    if (!form.username) {
        return false;
    }

    return true;
});
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="username" value="Username" />

                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                    @keydown="validateUsernameValue"
                    @keyup="validateUsernameValue"
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing || !allowToSubmit, 'cursor-not-allowed': !allowToSubmit }"
                    :disabled="form.processing || !allowToSubmit"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
