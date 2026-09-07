<script setup>
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUpdated, watch, computed } from "vue";

const props = defineProps({
    pagination: Object,
});

const elWrapper = ref(null);
const elPagination = ref(null);

const isOverflow = computed(() => {
    if (elWrapper.value?.offsetWidth < elPagination.value?.offsetWidth) {
        return true;
    }

    return false;
});
</script>

<template>
    <div ref="elWrapper" class="table-responsive">
        <div class="" id="example_paginate">
            <ul
                ref="elPagination"
                class="pagination"
                :class="{
                    'float-md-end justify-content-center': !isOverflow,
                    'float-start': isOverflow,
                }"
            >
                <li
                    v-for="link in pagination.links"
                    :key="link.label"
                    class="page-item"
                    :class="{
                        disabled: !link.url,
                        active: link.active,
                    }"
                >
                    <Link
                        :href="link.url ?? '#'"
                        tabindex="0"
                        class="page-link text-nowrap"
                        v-html="link.label"
                    />
                </li>
            </ul>
        </div>
    </div>
</template>
