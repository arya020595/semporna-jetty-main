<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
    listTab: Array,
    value: String,
});

const emits = defineEmits(["update:value"]);

const scrollPercent = ref(0);
const elScroller = ref(null);
const isScroll = ref(false);

const activeTab = computed({
    get() {
        return props.value;
    },
    set(value) {
        emits("update:value", value);

        if (isScroll.value) {
            const toPos = document.getElementById("tab-" + value).offsetLeft;
            elScroller.value.scrollTo(toPos - 56, 0);
            isScroll.value = false;
        }
    },
});

const handleScroll = (event) => {
    scrollPercent.value =
        event.currentTarget.scrollLeft /
        (event.currentTarget.scrollWidth - event.currentTarget.clientWidth);
};

const handleClickNavTab = (step) => {
    const currentIndex = props.listTab.findIndex(
        (item) => item.key == activeTab.value
    );

    if (
        currentIndex + step >= props.listTab.length ||
        currentIndex + step < 0
    ) {
        return false;
    }

    isScroll.value = true;
    activeTab.value = props.listTab[currentIndex + step].key;
};

defineExpose({ handleClickNavTab });
</script>

<template>
    <div class="arrow-wrapper">
        <div
            class="custom-arrow arrow-left"
            :class="{ 'd-none': scrollPercent == 0 }"
        >
            <button type="button" @click="handleClickNavTab(-1)">
                <span class="material-icons"> arrow_left </span>
            </button>
        </div>
        <div
            class="custom-arrow arrow-right"
            :class="{ 'd-none': scrollPercent == 1 }"
        >
            <button type="button" @click="handleClickNavTab(1)">
                <span class="material-icons"> arrow_right </span>
            </button>
        </div>
    </div>
    <div
        ref="elScroller"
        @scroll="handleScroll"
        class="custom-tabs-wrapper d-flex"
    >
        <div
            v-for="tab in listTab"
            :key="tab.key"
            class="custom-tab-item text-nowrap"
            :id="'tab-' + tab.key"
            :class="{ active: tab.key == activeTab }"
        >
            <button type="button" @click="activeTab = tab.key">
                {{ tab.title }}
            </button>
        </div>
    </div>
</template>
