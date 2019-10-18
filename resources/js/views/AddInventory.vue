<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Add Inventory') }}</heading>
        <card class="mb-6 py-3 px-6">
            <div class="flex border-b border-40">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Location') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="locationName"></p>
                </div>
            </div>
            <div class="flex">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Total Items') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90">
                        {{ itemCount }}
                        <span v-if="counter">
                            + {{ counter }} = {{ itemCount + counter }}
                        </span>
                    </p>
                </div>
            </div>
        </card>

        <div class="flex h-9 mb-6 items-center">
            <div class="relative flex-no-shrink">
                <icon
                    type="scan"
                    class="absolute search-icon-center ml-3 text-70"
                />

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
            <div
                v-show="!items.length"
                class="flex justify-center items-center text-50 px-6 py-8"
            >
                <div class="text-center">
                    <icon type="scan" width="50" height="50" class="mb-3" />
                    <h3 class="text-base font-normal text-80">
                        Scan the items you wish to add to location
                        {{ locationName }}.
                    </h3>
                </div>
            </div>
            <fetch-product-fields>
                <table
                    slot-scope="{ fields }"
                    v-show="items.length"
                    class="table w-full"
                    cellpadding="0"
                    cellspacing="0"
                >
                    <thead>
                        <tr>
                            <th class="text-left">GTIN</th>
                            <th v-for="field in fields" class="text-left">
                                {{ field.name }}
                            </th>
                            <th class="text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <fetch-product-information
                            v-for="(item, index) in items"
                            :key="999 - items.length + index"
                            :fields="fields"
                            :gtin="item.gtin"
                        >
                            <tr
                                slot-scope="{
                                    attributes,
                                    loading: loadingInfo,
                                }"
                            >
                                <td>{{ item.gtin }}</td>
                                <td v-for="field in fields">
                                    <span v-if="!loadingInfo">{{
                                        attributes[field.name]
                                    }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                        <loader
                                            v-show="!item.status"
                                            class="text-60"
                                            width="32"
                                        />
                                        <span
                                            v-if="item.status === 201"
                                            class="text-success-dark"
                                        >
                                            <icon type="checkmark-outline" />
                                        </span>
                                        <span
                                            v-if="item.status > 201"
                                            class="text-danger-dark"
                                        >
                                            <icon type="close-outline" />
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </fetch-product-information>
                    </tbody>
                </table>
            </fetch-product-fields>
        </card>
    </loading-view>
</template>

<script>
import { Minimum } from 'laravel-nova';
import FetchProductFields from './../components/FetchProductFields.vue';
import FetchProductInformation from './../components/FetchProductInformation.vue';

export default {
    components: {
        FetchProductFields,
        FetchProductInformation,
    },

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
            return this.location ? this.location.name : '';
        },
        itemCount() {
            return this.location ? this.location.inventory_count : '';
        },
    },

    methods: {
        async initializeComponent() {
            await this.getResource();

            if (!this.location) {
                return;
            }

            this.initialLoading = false;

            this.$nextTick(() => {
                this.$refs.gtin.focus();
            });
        },

        async getResource() {
            try {
                const { data } = await Minimum(
                    Nova.request().get(
                        `/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}`
                    )
                );

                return (this.location = data);
            } catch (error) {
                if (error.response.status === 404) {
                    this.$router.push({ name: '404' });
                }
            }
        },

        async addItem() {
            const newItem = {
                gtin: this.gtin,
                status: null,
            };

            if (newItem.gtin.trim() === '') {
                Nova.error(this.__('Please scan a valid barcode!'));

                return;
            }

            this.gtin = '';
            this.items.unshift(newItem);

            try {
                await Nova.request().post(
                    `/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}/add-inventory`,
                    newItem
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
