<script>
import Layout from "@/Layouts/AuthLayout.vue";
import { onMounted, watch } from "vue";
import { formatNumberOnly } from "../../Helpers/number";

export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import VAlert from "@/Shared/VAlert";

import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";

const props = defineProps({
    title: String,
    additional: Object,
});

const { email, urlBack, urlResend, urlSubmit } = props.additional;

const form = useForm({
    otp: "",
    code: props.additional.code,
});

watch(
    () => form.otp,
    async (newValue) => {
        let otp = await formatNumberOnly(newValue);
        form.otp = otp.substring(0, 5);
    }
);

watch(
    () => props.additional.code,
    (newValue) => {
        form.code = newValue;
    }
);

const login = () => {
    form.post(urlSubmit, {
        preserveScroll: true,
    });
};

const resend = () => {
    const formResend = useForm({});

    formResend.post(urlResend, {
        preserveScroll: true,
    });
};

onMounted(() => {
    if (!props.additional.code) {
        resend();
    }
});
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <form @submit.prevent="login" method="post">
        <div class="mb-3">
            <h3 class="text-center">OTP Verification</h3>
            <h6 class="text-center">Enter OTP sent to {{ email }}</h6>
        </div>

        <VAlert />

        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <input
                    class="form-control form-control-lg text-center otp-code"
                    v-model="form.otp"
                    placeholder="*****"
                />
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-4">
                <Link :href="urlBack" method="post" class="btn btn-secondary">
                    <span class="material-icons"> chevron_left </span>
                    Back
                </Link>
            </div>
            <div class="col-6 d-grid">
                <VButtonSubmit type="submit" :isProcessing="form.processing">
                    Submit
                </VButtonSubmit>
            </div>
        </div>
        <div class="d-grid mt-4">
            <div class="d-flex align-items-center justify-content-center">
                Don't get an email?
                <button
                    class="btn text-primary fw-bold text-decoration-underline"
                    type="button"
                    @click="resend"
                >
                    Resend OTP
                </button>
            </div>
        </div>
    </form>
</template>

<style>
.submit-wrapper {
    width: 150px;
}

.otp-code {
    letter-spacing: 1rem;
}
</style>
