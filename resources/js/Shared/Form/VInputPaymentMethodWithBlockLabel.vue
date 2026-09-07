<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    value: String | Number,
    error: String,
});

const emits = defineEmits(["update:value"]);

const paymentMethod = ref(props.value);

watch(
    () => props.value,
    (newValue) => {
        paymentMethod.value = newValue;
    }
);

watch(paymentMethod, (newValue) => {
    emits("update:value", newValue);
});
</script>

<template>
    <div class="row">
        <label
            for=""
            class="col-sm-4 label-size fw-bold mb-2 mb-sm-0 d-flex align-items-center"
            >{{ label }}</label
        >
        <div class="col-sm-8 d-flex align-items-center">
            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="radio"
                    name="inlineRadioOptions"
                    id="payment_method1"
                    value="visa"
                    v-model="paymentMethod"
                />
                <label class="form-check-label" for="payment_method1">
                    <img
                        src="/assets/images/icon_visa.png"
                        alt="visa"
                        style="height: 25px"
                    />
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="radio"
                    name="inlineRadioOptions"
                    id="payment_method2"
                    value="master-card"
                    v-model="paymentMethod"
                />
                <label class="form-check-label" for="payment_method2">
                    <img
                        src="/assets/images/icon_mastercard.png"
                        alt="master card"
                        style="height: 25px"
                    />
                </label>
            </div>
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
