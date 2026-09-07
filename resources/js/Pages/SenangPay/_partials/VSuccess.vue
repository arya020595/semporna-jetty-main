<script setup>
import VButton from "@/Shared/Buttons/VButton.vue";

const props = defineProps({
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
        <h6 class="fw-bold">Payment Successful</h6>
    </div>
    <div class="mb-3">Thank you! Your receipt has been sent to your email.</div>
    <div class="mb-3">
        Please bring your Draft Manifest Form to the relevant authorities for
        approval.
    </div>
    <div class="mb-3">
        You can check your approval status anytime through the system.
    </div>
    <div class="mb-4">Have a safe departure — and enjoy your trip!</div>
    <div class="mb-4">
        <VButton @click="clickProceed" btnStyle="btn btn-primary px-5">
            Proceed
        </VButton>
    </div>
</template>
