<template>
    <modal tabindex="-1" role="dialog" @modal-close="handleClose">
        <form
            autocomplete="off"
            @submit.prevent.stop="handleConfirm"
            class="action bg-white rounded-lg shadow-lg overflow-hidden w-action-fields"
        >
            <div>
                <heading :level="2" class="border-b border-40 py-8 px-8">{{
                    __('Choose New Location')
                }}</heading>
            </div>

            <form-belongs-to-field
                resource-name="inventories"
                :field="field"
            ></form-belongs-to-field>

            <div class="bg-30 px-6 py-3 flex">
                <div class="flex items-center ml-auto">
                    <button
                        type="button"
                        :disabled="processing"
                        @click.prevent="handleClose"
                        class="btn btn-link font-normal text-80 h-9 px-3 mr-3"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <progress-button
                        type="submit"
                        :disabled="processing"
                        :processing="processing"
                        >{{ __('Confirm') }}</progress-button
                    >
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
import _ from 'lodash';

export default {
    props: {
        processing: {
            type: Boolean,
            default: false,
        },
    },

    mounted() {
        if (document.querySelectorAll('.modal input').length) {
            document.querySelectorAll('.modal input')[0].focus();
        }
    },

    data: () => ({
        field: {
            attribute: 'location',
            name: 'Location',
            resourceName: 'locations',
            searchable: true,
        },
    }),

    methods: {
        handleConfirm() {
            let selectedResourceId = this.createFormData().get('location');

            if (selectedResourceId) {
                this.$emit('confirm', selectedResourceId);
            }
        },

        handleClose() {
            this.$emit('close');
        },

        createFormData() {
            return _.tap(new FormData(), formData => {
                this.field.fill(formData);
            });
        },
    },
};
</script>
