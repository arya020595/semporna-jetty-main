<script>
import Layout from "@/Layouts/BlankLayout.vue";
import VSuccess from "./_partials/VSuccess.vue";
import VFailed from "./_partials/VFailed.vue";
import VError from "./_partials/VError.vue";
export default {
    // Using shorthand syntax...
    layout: Layout,
};
</script>
<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";

let props = defineProps({
    title: String,
    additional: Object,
});

const { data, status, message, urlProceed } = props.additional;
</script>

<template>
    <Head>
        <title>{{ title }}</title>
    </Head>

    <div
        class="bg-image container-fluid min-vh-100 d-flex flex-column align-items-center justify-content-center"
    ></div>
    <!-- Modal Overlay -->
    <div class="modal show d-block" tabindex="-1" aria-hidden="true">
        <div
            class="overlay bg-black bg-opacity-50 flex items-center justify-center z-50"
        ></div>
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-body px-3 py-4 text-center">
                    <VSuccess
                        v-if="status == 'success'"
                        :urlProceed="urlProceed"
                    />
                    <VFailed
                        v-if="status == 'failed'"
                        :message="message"
                        :urlProceed="urlProceed"
                    />
                    <VError v-if="status == 'error'" :urlProceed="urlProceed" />
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="css" scoped>
.overlay {
    top: 0px;
    bottom: 0px;
    right: 0px;
    left: 0px;
    position: fixed;
}

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
