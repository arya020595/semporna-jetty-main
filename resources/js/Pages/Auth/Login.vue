<script>
import Layout from "@/Layouts/AuthLayout.vue";
import VInputWithBlockLabel from "../../Shared/Form/VInputWithBlockLabel.vue";
import VInputPasswordWithBlockLabel from "../../Shared/Form/VInputPasswordWithBlockLabel.vue";
import { useRoleStore } from "../../Store/role";

export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import axios from "axios";
import VAlert from "@/Shared/VAlert";
import { VueRecaptcha } from "vue-recaptcha";

import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";

const props = defineProps({
    title: String,
    additional: Object,
});

const roleStore = useRoleStore();

if (!roleStore.getRole() && !props.additional.role_id) {
    router.get("/");
}

const showPassword = ref(false);
const elRecaptcha = ref(null);

const form = useForm({
    email: "",
    password: "",
    remember_me: false,
    recaptcha: "",
    role_id: props.additional.role_id ?? roleStore.getRole(),
});

const recaptchav2_sitekey = computed(() => usePage().props.recaptchav2_sitekey);

const isError = ref(false);

const onCaptchaVerify = (token) => {
    // Provide the token response to the form object.
    form.recaptcha = token;
};

const login = function () {
    form.post("/login", {
        preserveScroll: true,
        onError: (errors) => {
            if (form.recaptcha) {
                elRecaptcha.value.reset();
            }
        },
    });
};
</script>

<template>
    <Head>
        <title>Login</title>
        <meta name="description" content="Welcome to Lembaga Koko Malaysia" />
    </Head>

    <form @submit.prevent="login" method="post">
        <div class="alert alert-danger" v-if="isError">
            Email or Password not match!
        </div>

        <VAlert :isShowValidation="false" />

        <div class="mb-3">
            <VInputWithBlockLabel
                elId="email"
                label="Email"
                placeholder="Email"
                v-model:value="form.email"
                type="email"
                :error="form.errors.email"
            />
        </div>

        <div class="mb-3">
            <VInputPasswordWithBlockLabel
                elId="password"
                label="Password"
                placeholder="Password"
                v-model:value="form.password"
                :error="form.errors.password"
            />
        </div>

        <div class="form-check mb-3">
            <input
                class="form-check-input"
                type="checkbox"
                v-model="form.remember_me"
                id="rememberPasswordCheck"
            />
            <label class="form-check-label" for="rememberPasswordCheck">
                Remember password
            </label>
        </div>
        <!--
        <div class="mb-3">
            <VueRecaptcha
                :sitekey="recaptchav2_sitekey"
                ref="elRecaptcha"
                @verify="onCaptchaVerify"
            />
            <div v-if="form.errors.recaptcha" class="row">
                <div class="col-12 text-danger font-error">
                    {{ form.errors.recaptcha }}
                </div>
            </div>
        </div> -->

        <div class="row justify-content-between">
            <div class="col-6">
                <Link href="/" class="btn btn-secondary">
                    <span class="material-icons"> chevron_left </span>
                    Back
                </Link>
            </div>
            <div class="col-6 d-grid">
                <VButtonSubmit type="submit" :isProcessing="form.processing">
                    Login
                </VButtonSubmit>
            </div>
        </div>
        <div class="d-grid mt-4">
            <div class="text-center">
                Don't have an account?
                <Link
                    class="text-primary fw-bold text-decoration-underline"
                    href="/register"
                >
                    Register Here
                </Link>
            </div>
        </div>
        <hr class="my-3" />
        <div class="d-grid mb-2">
            <div class="text-center">
                Forgot Password ?
                <Link
                    class="text-primary fw-bold text-decoration-underline"
                    href="/forgot-password"
                >
                    Click Here
                </Link>
            </div>
        </div>

        <div class="mt-5 text-center terms-condition">
            <a
                href="http://app.senangpay.my/policy/7631760066150411"
                target="_blank"
                class="text-nowrap text-decoration-none"
                >Terms and conditions</a
            >
            |
            <a
                href="http://app.senangpay.my/policy/7631760066150413"
                target="_blank"
                class="text-nowrap text-decoration-none"
                >Refund Policy</a
            >
            |
            <a
                href="http://app.senangpay.my/policy/7631760066150412"
                target="_blank"
                class="text-nowrap text-decoration-none"
                >Privacy Policy</a
            >
        </div>
    </form>
</template>

<style>
.submit-wrapper {
    width: 150px;
}

.terms-condition {
    font-size: 0.8rem;
}
</style>
