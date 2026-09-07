<script setup>
import { computed } from "vue";

const props = defineProps({
    adult: Number,
    child: Number,
    ticket_adult: Number,
    ticket_child: Number,
    adult_fee: Number,
    child_fee: Number,
});

const adultFeeString = computed(() => {
    return props.adult_fee > 0
        ? "x RM " + parseFloat(props.adult_fee).toFixed(2)
        : "Free";
});

const childFeeString = computed(() => {
    return props.child_fee > 0
        ? "x RM " + parseFloat(props.child_fee).toFixed(2)
        : "Free";
});

const formatValue = (value, ticket) => {
    const ticketText = (ticket ?? 0) > 0 ? `( -${ticket} Ticket )` : "";

    return `${value} ${ticketText}`;
};

const total = computed(() => {
    const adult = props.adult ?? 0;
    const child = props.child ?? 0;

    const ticket_adult = props.ticket_adult ?? 0;
    const ticket_child = props.ticket_child ?? 0;

    const calculate =
        (adult - ticket_adult) * props.adult_fee +
        (child - ticket_child) * props.child_fee;

    return "RM " + parseFloat(calculate).toFixed(2);
});
</script>
<template>
    <div class="row">
        <div class="col-6 mb-3">
            <div class="row align-items-sm-center">
                <label
                    :class="'col-sm-4 label-size text-sm-end fw-bold mb-sm-0 mb-2'"
                >
                    Adult
                </label>
                <div :class="'col-sm-8 custom-position-relative'">
                    <input
                        id="adult"
                        type="text"
                        class="form-control"
                        :value="formatValue(adult, ticket_adult)"
                        disabled
                    />
                </div>
            </div>
        </div>
        <div class="col-sm-4 col-6 mb-3">
            <input
                type="text"
                class="form-control-plaintext"
                :value="adultFeeString"
                disabled
            />
        </div>
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <div class="row align-items-sm-center">
                <label
                    :class="'col-sm-4 label-size text-sm-end fw-bold mb-sm-0 mb-2'"
                >
                    Child
                </label>
                <div :class="'col-sm-8 custom-position-relative'">
                    <input
                        type="text"
                        class="form-control"
                        :value="formatValue(child, ticket_child)"
                        disabled
                    />
                </div>
            </div>
        </div>
        <div class="col-6 mb-3">
            <input
                type="text"
                class="form-control-plaintext"
                :value="childFeeString"
                disabled
            />
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 offset-sm-2 col-12">
            <hr class="my-2" style="border: 1px solid #000; opacity: 0.5" />
        </div>
    </div>

    <div class="row">
        <div class="col-6 mb-3 mt-3">
            <div class="row align-items-sm-center">
                <label
                    :class="'col-sm-4 label-size text-sm-end fw-bold mb-sm-0 mb-2'"
                >
                    Total
                </label>
                <div :class="'col-sm-8 custom-position-relative'">
                    <input
                        type="text"
                        class="form-control bg-primary text-white"
                        :value="total"
                        disabled
                    />
                </div>
            </div>
        </div>
    </div>
</template>
