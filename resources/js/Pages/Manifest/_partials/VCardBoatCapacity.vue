<script setup>
const props = defineProps({
    capacity: Number,
    totalPassenger: Number,
});
</script>

<template>
    <div class="row">
        <div class="col-6 col-lg-3 col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-header text-center text-nowrap">
                    Boat Capacity
                </div>
                <div class="card-body">
                    <h2 class="text-center">{{ capacity ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-header text-center text-nowrap">
                    Total Passenger
                </div>
                <div class="card-body">
                    <h2 class="text-center">{{ totalPassenger ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
</template>
