<script setup>
defineProps({
    filters: { type: Object, required: true },
});

defineEmits(["applyFilters", "resetFilters"]);
</script>

<template>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"
                        >Departure Date From</label
                    >
                    <input
                        type="date"
                        class="form-control"
                        :value="filters.date_from"
                        @input="filters.date_from = $event.target.value"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold"
                        >Departure Date To</label
                    >
                    <input
                        type="date"
                        class="form-control"
                        :value="filters.date_to"
                        :min="filters.date_from"
                        @input="filters.date_to = $event.target.value"
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Payment Status</label>
                    <select
                        class="form-select"
                        :value="filters.status_filter"
                        @change="filters.status_filter = $event.target.value"
                    >
                        <option value="">All Status</option>
                        <option value="1">Paid</option>
                        <option value="0">Pending</option>
                        <option value="-1">Failed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button
                        class="btn btn-primary flex-fill d-inline-flex align-items-center justify-content-center gap-1"
                        @click="$emit('applyFilters')"
                    >
                        <span class="material-icons" style="font-size: 18px"
                            >search</span
                        >
                        Search
                    </button>
                    <button
                        class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center"
                        @click="$emit('resetFilters')"
                        title="Reset"
                    >
                        <span class="material-icons" style="font-size: 18px"
                            >refresh</span
                        >
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
