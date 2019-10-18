<template>
    <loading-view :loading="loading">
        <heading class="mb-3">{{ __('Add Inventory') }}</heading>
        <card>
            <choose-location @selected="selected"></choose-location>
        </card>
    </loading-view>
</template>

<script>
export default {
    mounted() {
        this.loading = false;
    },

    data: () => ({
        loading: true,
    }),

    methods: {
        selected(e) {
            this.$router.push({
                name: 'locations.add-inventory',
                params: { locationId: e },
            });
        },
    },
};
</script>
