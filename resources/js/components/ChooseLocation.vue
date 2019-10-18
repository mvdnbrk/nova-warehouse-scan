<template>
    <form @submit.prevent="next" autocomplete="off">
        <form-belongs-to-field
            resource-name="inventories"
            :field="field"
        ></form-belongs-to-field>

        <div class="bg-30 flex items-center px-8 py-4">
            <progress-button class="ml-auto" type="submit">{{
                __('Next')
            }}</progress-button>
        </div>
    </form>
</template>

<script>
import _ from 'lodash';

export default {
    data: () => ({
        field: {
            attribute: 'location',
            name: 'Location',
            resourceName: 'locations',
            searchable: true,
        },
    }),

    methods: {
        next() {
            let selectedResourceId = this.createFormData().get('location');

            if (selectedResourceId) {
                this.$emit('selected', selectedResourceId);
            }
        },

        createFormData() {
            return _.tap(new FormData(), formData => {
                this.field.fill(formData);
            });
        },
    },
};
</script>
