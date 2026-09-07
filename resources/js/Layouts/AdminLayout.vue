<script setup>
import Sidenav from "../Shared/Layouts/Sidenav.vue";
import Navbar from "../Shared/Layouts/Navbar.vue";
import { onMounted } from "vue";

onMounted(() => {
    const dismissable = document.getElementById("layoutSidenav_content");

    dismissable?.addEventListener("click", (e) => {
        const toggleButton = document.getElementById("sidebarToggle");
        if (isSidenavOpen()) {
            toggleButton.click();
        }
    });
});

const isSidenavOpen = () => {
    return (
        document.body.classList.contains("sb-sidenav-toggled") &&
        window.innerWidth < 992
    );
};
</script>

<template>
    <div class="sb-nav-fixed">
        <Navbar />
        <div id="layoutSidenav">
            <Sidenav />

            <div id="layoutSidenav_content">
                <main class="mb-5">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
#layoutSidenav_content {
    background: #f5f5f5;
}
</style>
