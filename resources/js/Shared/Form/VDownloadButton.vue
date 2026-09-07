<script setup>
const props = defineProps({
    value: Object,
});
</script>

<template>
    <a
        class="btn btn-success mx-1"
        :class="{ 'd-none': !value }"
        target="_blank"
        :href="existing"
    >
        <span class="material-icons">visibility</span>
    </a>
</template>
