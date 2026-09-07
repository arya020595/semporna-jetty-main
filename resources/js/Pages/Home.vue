<script>
import Layout from "@/Layouts/BlankLayout.vue";
import { onMounted } from "vue";
import Swal from "sweetalert2";

export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import VButton from "../Shared/Buttons/VButton.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { useRoleStore } from "../Store/role";
import {
    ROLE_ADMINISTRATOR,
    ROLE_AUTHORITIES,
    ROLE_OPERATOR,
    ROLE_OPERATOR_JETTY,
} from "../Config/role";

const appBaseUrl = usePage().props.appBaseUrl;
const roleStore = useRoleStore();

const login = (role) => {
    roleStore.setRole(role);
    router.get("/login");
};

// onMounted(() => {
//     Swal.fire({
//         icon: "warning",
//         title: "Semporna Jetty is currently down for maintenance",
//         confirmButtonText: "Okay",
//     });
// });
</script>

<template>
    <Head>
        <title>Welcome</title>
        <meta name="description" content="Welcome to Semporna Jetty" />
    </Head>

    <div
        class="bg-image container-fluid min-vh-100 d-flex flex-column align-items-center justify-content-center"
    >
        <div class="text-center bg-white p-4 select-wrapper">
            <div class="mb-3">
                <div
                    class="mx-auto d-flex justify-content-center align-items-center"
                >
                    <img
                        :src="
                            appBaseUrl +
                            '/assets/images/Coat_of_arms_of_Sabah.jpg'
                        "
                        class="me-3 logo-sabah"
                        alt="STB Logo"
                    />
                    <img
                        id="logo"
                        :src="appBaseUrl + '/assets/images/semporna-logo.png'"
                        class="logo-semporna"
                        alt="Semporna Logo"
                    />
                </div>
                <div id="logo-title" class="text-center"></div>
            </div>
            <h5 class="fw-bold mb-3">Select a Role</h5>
            <div class="row">
                <div class="col-sm-6 px-1 d-flex flex-column">
                    <VButton
                        @onClick="login(ROLE_OPERATOR)"
                        btnStyle="btn-primary mb-3 py-2"
                    >
                        Tour Operator
                    </VButton>
                </div>

                <div class="col-sm-6 px-1 d-flex flex-column">
                    <VButton
                        @onClick="login(ROLE_AUTHORITIES)"
                        btnStyle="btn-primary mb-3 py-2"
                    >
                        Authorities
                    </VButton>
                </div>
                <div class="col-sm-6 px-1 d-flex flex-column">
                    <VButton
                        @onClick="login(ROLE_OPERATOR_JETTY)"
                        btnStyle="btn-primary mb-3 py-2"
                    >
                        Jetty Operator
                    </VButton>
                </div>
                <div class="col-sm-6 px-1 d-flex flex-column">
                    <VButton
                        @onClick="login(ROLE_ADMINISTRATOR)"
                        btnStyle="btn-primary mb-3 py-2"
                    >
                        Administrator
                    </VButton>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.bg-image {
    background-image: url("/assets/images/semporna-backdrop.jpg");
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

.logo-sabah {
    height: 140px;
}

.logo-semporna {
    height: 150px;
}

.select-wrapper {
    max-width: 400px;
    border-radius: 20px;
}
</style>
