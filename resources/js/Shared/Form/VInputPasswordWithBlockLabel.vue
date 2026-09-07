<script setup>
import { ref } from "vue";

const props = defineProps({
    elId: {
        Type: String,
        default: "",
    },
    label: String,
    placeholder: String,
    error: String,
    value: String,
});

const inputValue = ref("");
const showPassword = ref(false);

const tooglePassword = function () {
    showPassword.value = !showPassword.value;
};

defineEmits(["update:value"]);
</script>

<template>
    <div class="row align-items-sm-center">
        <label :for="elId" class="col-12 label-size fw-bold mb-2">
            {{ label }}
        </label>
        <div class="col-129">
            <div class="d-flex align-items-center">
                <input
                    :id="elId"
                    :type="showPassword ? 'text' : 'password'"
                    class="form-control"
                    @input="$emit('update:value', $event.target.value)"
                    :class="{ 'is-invalid': error }"
                    :value="value"
                    :placeholder="placeholder"
                />
                <i
                    class="fas fa-eye toogle-password"
                    :class="{
                        'fa-eye': showPassword,
                        'fa-eye-slash': !showPassword,
                    }"
                    @click="tooglePassword"
                ></i>
            </div>
        </div>
    </div>
    <div v-if="error" class="row">
        <div class="col-12 text-danger font-error">
            {{ error }}
        </div>
    </div>
</template>
<style>
input[type="password"]::-webkit-input-decoration,
input[type="password"]::-webkit-credentials-auto-fill-button,
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
    display: none !important;
}
.toogle-password {
    margin-left: -30px;
    cursor: pointer;
    color: #929aac;
}
</style>
