<script>
import Layout from "@/Layouts/RegisterLayout.vue";
import { formatAlphaNumericOnly } from "../../Helpers/number";
import { debounce } from "lodash";

export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { useRoleStore } from "../../Store/role";
import { computed, ref, watch } from "vue";
import VAlert from "@/Shared/VAlert";
import { VueRecaptcha } from "vue-recaptcha";
import VInputWithBlockLabel from "../../Shared/Form/VInputWithBlockLabel.vue";
import VInputPasswordWithBlockLabel from "../../Shared/Form/VInputPasswordWithBlockLabel.vue";
import VButtonSubmit from "@/Shared/Buttons/VButtonSubmit";
import VSelectDefaultWithBlockLabel from "../../Shared/Form/VSelectDefaultWithBlockLabel.vue";
import {
    ARR_AUTHORITIES,
    ROLE_AUTHORITIES,
    ROLE_OPERATOR,
    ROLE_OPERATOR_JETTY,
} from "../../Config/role";

const props = defineProps({
    title: String,
    additional: Object,
});

const { urlSubmit, arrJetty } = props.additional;

const roleStore = useRoleStore();
const roleType = roleStore.getRole();

if (!roleType) {
    router.get("/");
}

const elRecaptcha = ref(null);

const form = useForm({
    staf_id: "",
    name: "",
    email: "",
    username: "",
    ic_no: "",
    ssm_no: "",
    password: "",
    password_confirmation: "",
    terms_condition: false,
    recaptcha: "",
    role: roleType < 10 ? roleType : "",
    jetty_id: "",
});

const recaptchav2_sitekey = computed(() => usePage().props.recaptchav2_sitekey);

const onCaptchaVerify = (token) => {
    // Provide the token response to the form object.
    form.recaptcha = token;
};

const login = function () {
    form.post(urlSubmit, {
        preserveScroll: true,
        onError: (errors) => {
            if (form.recaptcha) {
                elRecaptcha.value.reset();
            }
        },
    });
};

watch(
    () => form.ic_no,
    async (newValue) => {
        form.ic_no = await formatAlphaNumericOnly(newValue);
    }
);
</script>

<template>
    <Head>
        <title>Register Account</title>
        <meta name="description" content="Welcome to Lembaga Koko Malaysia" />
    </Head>

    <form @submit.prevent="login" method="post" class="pt-5">
        <h3 class="mt-3 mb-5">Register Account</h3>

        <VAlert :isShowValidation="false" />

        <!-- <div v-if="roleType == ROLE_AUTHORITIES" class="mb-2">
            <VInputWithBlockLabel
                elId="staf_id"
                label="Employee ID"
                placeholder="Employee ID"
                v-model:value="form.staf_id"
                :error="form.errors.staf_id"
            />
        </div> -->

        <div class="mb-2">
            <VInputWithBlockLabel
                elId="name"
                :label="roleType == ROLE_OPERATOR ? 'Company Name' : 'Fullname'"
                :placeholder="
                    roleType == ROLE_OPERATOR ? 'Company Name' : 'Fullname'
                "
                v-model:value="form.name"
                :error="form.errors.name"
            />
        </div>

        <div v-if="roleType == ROLE_OPERATOR" class="mb-2">
            <VInputWithBlockLabel
                elId="username"
                label="Username"
                placeholder="Username"
                v-model:value="form.username"
                :error="form.errors.username"
            />
        </div>

        <div class="mb-2">
            <VInputWithBlockLabel
                elId="ic_no"
                :label="roleType == ROLE_OPERATOR ? 'SSM Number' : 'IC No.'"
                :placeholder="
                    roleType == ROLE_OPERATOR ? 'SSM Number' : 'IC Number'
                "
                type="text"
                v-model:value="form.ic_no"
                :error="form.errors.ic_no"
            />
        </div>

        <div class="mb-2">
            <VInputWithBlockLabel
                elId="email"
                :label="roleType == ROLE_OPERATOR ? 'Company Email' : 'Email'"
                :placeholder="
                    roleType == ROLE_OPERATOR
                        ? 'Company Email'
                        : 'Email Address'
                "
                v-model:value="form.email"
                type="email"
                :error="form.errors.email"
            />
        </div>

        <div class="mb-2">
            <VInputPasswordWithBlockLabel
                elId="password"
                label="Password"
                placeholder="Password"
                v-model:value="form.password"
                :error="form.errors.password"
            />
        </div>

        <div class="mb-2">
            <VInputPasswordWithBlockLabel
                elId="password_confirmation"
                label="Confirm Password"
                placeholder="Confirm Password"
                v-model:value="form.password_confirmation"
                :error="form.errors.password_confirmation"
            />
        </div>

        <div v-if="roleType == ROLE_OPERATOR_JETTY || roleType == ROLE_AUTHORITIES" class="mb-2">
            <VSelectDefaultWithBlockLabel
                elId="jetty_id"
                label="Jetty"
                placeholder="Select Jetty"
                :options="
                    arrJetty.map((item) => ({
                        id: item.id,
                        description: item.title,
                    }))
                "
                v-model:value="form.jetty_id"
                :error="form.errors.jetty_id"
            />
        </div>

        <div v-if="roleType == ROLE_AUTHORITIES" class="mb-2">
            <VSelectDefaultWithBlockLabel
                elId="role"
                label="Authorities"
                placeholder="Select Authorities"
                :options="ARR_AUTHORITIES"
                v-model:value="form.role"
                :error="form.errors.role"
            />
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="form.terms_condition"
                    id="termsConditionCheck"
                />
                <label class="form-check-label" for="termsConditionCheck">
                    Accept their terms and conditions
                </label>
            </div>
            <div v-if="form.errors.terms_condition" class="row">
                <div class="col-12 text-danger font-error">
                    {{ form.errors.terms_condition }}
                </div>
            </div>
        </div>

        <!-- <div class="mb-3">
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

        <div class="d-grid mx-auto submit-wrapper">
            <VButtonSubmit type="submit" :isProcessing="form.processing">
                Register
            </VButtonSubmit>
        </div>
        <div class="d-grid mt-4 mb-3">
            <div class="text-center">
                Already have an account?
                <Link
                    class="text-primary fw-bold text-decoration-underline"
                    href="/login"
                >
                    Click Here
                </Link>
                to login
            </div>
        </div>
    </form>
</template>

<style>
.submit-wrapper {
    width: 150px;
}
</style>
