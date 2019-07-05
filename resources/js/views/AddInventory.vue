<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Add Inventory') }}</heading>
        <card class="flex mb-6 px-6 py-6">
            <div class="w-1/4">
                <h4 class="font-normal text-80">{{ __('Location') }}</h4>
            </div>
            <div class="w-3/4">
                <p class="text-90">{{ locationName }}</p>
            </div>
        </card>

        <div class="flex h-9 mb-6 items-center">
            <div class="relative flex-no-shrink">
                <icon type="scan" class="absolute search-icon-center ml-3 text-70" />

                <input
                    class="appearance-none form-control form-input w-search pl-search"
                    ref="gtin"
                    min="0"
                    type="number"
                    placeholder="Scan barcode"
                    v-model="gtin"
                    @keydown.enter="addItem"
                />
            </div>

            <div class="ml-auto flex items-center mr-6">
                <span class="text-80 mr-3">Items scanned:</span>
                <heading :level="3">{{ counter }}</heading>
            </div>
        </div>

        <card>
            <div v-show="!items.length" class="flex justify-center items-center text-50 px-6 py-8">
                <div class="text-center">
                    <icon type="scan" width="50" height="50" class="mb-3" />
                    <h3 class="text-base font-normal text-80">
                        Scan the items you wish to add to location {{ locationName }}.
                    </h3>
                </div>
            </div>
            <table v-show="items.length" class="table w-full" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-left">GTIN</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items" :key="index">
                        <td>{{ item.gtin }}</td>
                        <td class="text-center">
                            <loader v-show="!item.status" class="text-60" width="32" />
                            <span v-if="item.status === 201" class="text-success-dark">
                                <icon type="checkmark-outline" />
                            </span>
                            <span v-if="item.status > 201" class="text-danger-dark">
                                <icon type="close-outline" />
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </card>
    </loading-view>
</template>

<script>
import _ from 'lodash';
import { Minimum } from 'laravel-nova';

export default {
    props: {
        locationId: {
            required: true,
        },
    },

    mounted() {
        this.initializeComponent();
    },

    data: () => ({
        counter: 0,
        gtin: '',
        location: null,
        items: [],
        initialLoading: true,
    }),

    computed: {
        locationName() {
            return this.location ? _.find(this.location.fields, ['attribute', 'name']).value : '';
        },
    },

    methods: {
        async initializeComponent() {
            await this.getResource();

            this.initialLoading = false;

            if (!this.location) {
                return;
            }

            this.$nextTick(() => {
                this.$refs.gtin.focus();
            });
        },

        async getResource() {
            try {
                const {
                    data: { resource },
                } = await Minimum(Nova.request().get(`/nova-api/locations/${this.locationId}`));

                return (this.location = resource);
            } catch (error) {
                if (error.response.status === 404) {
                    this.$router.push({ name: '404' });

                    return;
                }
            }
        },

        async addItem() {
            const newItem = {
                gtin: this.gtin,
                status: null,
            };

            if (newItem.gtin.trim() === '') {
                this.$toasted.show(this.__('Please scan a valid barcode!'), { type: 'error' });

                return;
            }

            this.gtin = '';
            this.items.unshift(newItem);

            try {
                await Minimum(
                    Nova.request().post(
                        `/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}/add-inventory`,
                        newItem
                    )
                );

                newItem.status = 201;

                this.counter++;
            } catch (error) {
                newItem.status = error.response.status;
            }
        },
    },
};
</script>
