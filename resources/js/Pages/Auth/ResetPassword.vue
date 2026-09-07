<script>
import Layout from "@/Layouts/AuthLayout.vue";

export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import VAlert from "@/Shared/VAlert";
import VInputPassword from "../../Shared/Form/VInputPassword.vue";

const props = defineProps({
    email: String,
    token: String,
    url_submit: String,
});

const showPassword = ref(false);
const form = useForm({
    email: props.email,
    token: props.token,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(props.url_submit, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head>
        <title>Login</title>
        <meta name="description" content="Welcome to Lembaga Koko Malaysia" />
    </Head>

    <form @submit.prevent="submit">
        <h3 class="text-center mb-3">Reset Password</h3>

        <VAlert />

        <div class="form-group mb-3">
            <input
                type="email"
                name="email"
                v-model="form.email"
                class="form-control"
                readonly
                placeholder="Email"
            />
        </div>
        <div class="mb-3">
            <VInputPassword
                elId="new_password"
                placeholder="New Password"
                v-model:value="form.password"
                :error="form.errors.password"
                autocomplete="off"
            />
        </div>
        <div class="mb-3">
            <VInputPassword
                elId="password"
                placeholder="New Password Confirmation"
                v-model:value="form.password_confirmation"
                :error="form.errors.password_confirmation"
                autocomplete="off"
            />
        </div>

        <div class="d-grid">
            <button
                class="btn btn-primary btn-login"
                type="submit"
                :disabled="form.processing"
            >
                Submit New password
                <div
                    v-if="form.processing"
                    class="spinner-border spinner-border-sm ms-2"
                >
                    <span class="visually-hidden">Loading...</span>
                </div>
            </button>
        </div>
        <hr class="my-4" />
        <div class="d-grid mb-2">
            <Link class="btn text-uppercase fw-bold" href="/login">
                <i class="fas fa-key me-2"></i> Back to Login Page
            </Link>
        </div>
    </form>
</template>

<style>
.toogle-password {
    margin-left: -30px;
    cursor: pointer;
    color: #929aac;
}
</style>
