<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">Scan</heading>
    </loading-view>
</template>

<script>
export default {
    created() {
        this.initialLoading = false;
    },

    data() {
        this.initialLoading = true;
    },
};
</script>
