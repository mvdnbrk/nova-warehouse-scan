<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Move Inventory') }}</heading>
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
                    <p class="text-90" v-text="inventoryCount"></p>
                </div>
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
                    @keydown.enter="selectItem"
                    :disabled="this.inventoryCount === 0"
                />
            </div>

            <div class="ml-auto flex items-center mr-6">
                <span class="text-80 mr-3">{{ __('Items scanned') }}:</span>
                <heading :level="3">{{ counter }}</heading>
            </div>
        </div>

        <card>
            <div
                v-show="!selectedItems.length"
                class="flex justify-center items-center text-50 px-6 py-8"
            >
                <div class="text-center">
                    <icon type="scan" width="50" height="50" class="mb-3" />
                    <h3 class="text-base font-normal text-80" v-if="this.inventory.length">
                        {{ __('Scan the items you wish to move to another location.') }}
                    </h3>
                    <h3 class="text-base font-normal text-80" v-if="!this.inventory.length">
                        {{ __('There are no inventory items at this location.') }}
                    </h3>
                </div>
            </div>

            <fetch-product-fields>
                <table
                    slot-scope="{ fields }"
                    v-show="selectedItems.length"
                    class="table w-full"
                    cellpadding="0"
                    cellspacing="0"
                >
                    <thead>
                        <tr>
                            <th class="text-left">GTIN</th>
                            <th v-for="field in fields" class="text-left">{{ field.name }}</th>
                            <th class="text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <fetch-product-information v-for="(item, index) in selectedItems" :key="999 - selectedItems.length + index" :fields="fields" :gtin="item.gtin">
                            <tr slot-scope="{ attributes, loading: loadingInfo }">
                                <td>{{ item.gtin }}</td>
                                <td v-for="field in fields">
                                    <span v-if="! loadingInfo">{{ attributes[field.name] }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center">
                                        <span v-if="item.status === 201" class="text-success-dark">
                                            <icon type="checkmark-outline" />
                                        </span>
                                        <button
                                            class="text-danger-dark"
                                            v-if="item.status > 201"
                                            @click="removeSelectedItemAt(index)"
                                        >
                                            <icon type="close-outline" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </fetch-product-information>
                    </tbody>
                </table>
            </fetch-product-fields>

            <div v-show="selectedItems.length" class="bg-30 flex items-center px-8 py-4">
                <a @click="reset" class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">
                    {{ __('Reset') }}
                </a>
                <progress-button
                    class="ml-3"
                    :disabled="!selectedItems.length"
                    @click.native="openModal"
                >
                    {{ __('Choose New Location') }}
                </progress-button>
            </div>
        </card>

        <portal to="modals">
            <transition name="fade">
                <confirm-new-location-modal
                    v-if="isModalOpen"
                    @confirm="confirm"
                    @close="closeModal"
                    :processing="processing"
                ></confirm-new-location-modal>
            </transition>
        </portal>
    </loading-view>
</template>

<script>
import _ from 'lodash';
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

    computed: {
        locationName() {
            return this.location ? this.location.name : '';
        },
    },

    data: () => ({
        gtin: '',
        counter: 0,
        location: null,
        isModalOpen: false,
        initialLoading: true,
        inventory: [],
        inventoryCount: 0,
        selectedItems: [],
        processing: false,
    }),

    methods: {
        selectItem() {
            const selectedItem = {
                gtin: this.gtin,
                status: 404,
            };

            this.gtin = '';
            this.selectedItems.unshift(selectedItem);

            _.findIndex(this.inventory, (item, index) => {
                if (item === selectedItem.gtin) {
                    this.inventory.splice(index, 1);
                    this.counter++;
                    selectedItem.status = 201;
                }

                return item === selectedItem.gtin;
            });
        },

        async reset() {
            this.initialLoading = true;
            await Minimum(this.getInventory(), 500);
            this.selectedItems = [];
            this.counter = 0;
            this.initialLoading = false;
            this.setFocus();
        },

        async initializeComponent() {
            await Promise.all([this.getResource(), this.getInventory()]);

            if (!this.location) {
                return;
            }

            this.initialLoading = false;

            this.setFocus();
        },

        async getResource() {
            try {
                const { data } = await Minimum(Nova.request().get(`/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}`));

                return (this.location = data);
            } catch (error) {
                if (error.response.status === 404) {
                    this.$router.push({ name: '404' });
                }
            }
        },

        async getInventory() {
            this.inventory = [];

            try {
                const {
                    data: { items, count },
                } = await Nova.request().get(
                    `/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}/inventory`
                );

                this.inventoryCount = count;

                return (this.inventory = items);
            } catch (error) {
                //
            }
        },

        removeSelectedItemAt(index) {
            this.selectedItems.splice(index, 1);
        },

        setFocus() {
            this.$nextTick(() => {
                this.$refs.gtin.focus();
            });
        },

        async confirm(e) {
            try {
                this.processing = true;

                await Minimum(
                    Nova.request().post(
                        `/nova-vendor/mvdnbrk/warehouse-scan/locations/${this.locationId}/move-inventory`,
                        {
                            newLocationId: e,
                            items: this.selectedItems,
                        }
                    ),
                    500
                );

                this.closeModal();
                this.processing = false;

                await this.reset();

                Nova.success(this.__('Inventory was moved!'));
            } catch (error) {
                Nova.error(this.__(error.response.data.message));

                this.processing = false;
            }
        },

        openModal() {
            this.isModalOpen = true;
        },

        closeModal() {
            this.isModalOpen = false;
        },
    },
};
</script>
