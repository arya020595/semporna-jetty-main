<script setup>
import VButton from "@/Shared/Buttons/VButton.vue";

const props = defineProps({
    message: String,
    urlProceed: String,
});

const clickProceed = () => {
    window.location.href = props.urlProceed;
};
</script>

<template>
    <div class="mb-4">
        <img
            src="/assets/images/icon_payment_successful.png"
            alt="paynow button"
            style="max-width: 200px; width: 100%"
        />
    </div>
    <div class="mb-3">
        <h6 class="fw-bold">Payment Failed</h6>
    </div>
    <div class="mb-3">
        {{ message }}
    </div>
    <div class="mb-4">Thank you!!!</div>
    <div class="mb-4">
        <VButton @click="clickProceed" btnStyle="btn btn-primary px-5">
            Proceed
        </VButton>
    </div>
</template>
