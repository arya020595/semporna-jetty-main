<script setup>
import VShowPassenger from "../../../Shared/Manifest/VShowPassenger.vue";

const props = defineProps({
    manifest: Object,
});
</script>

<template>
    <h6 class="bg-primary text-white px-4 py-3 mb-3">
        Boat Passenger Information
    </h6>

    <VShowPassenger
        v-for="(passenger, index) in manifest.passengers"
        :key="'passenger_' + index"
        :index="index"
        :value="passenger"
    />

    <h6
        v-if="manifest.staff?.length"
        class="bg-primary text-white px-4 py-3 mb-3"
    >
        Boat Staff Information
    </h6>

    <VShowPassenger
        v-for="(passenger, index) in manifest.staff"
        :key="'staff_' + index"
        :index="index"
        :value="passenger"
        isStaff="true"
    />
</template>
