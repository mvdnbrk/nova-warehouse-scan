<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Pick Order') }}</heading>
        <card class="mb-6 py-3 px-6">
            <div
                class="flex"
                :class="!hasError && 'border-b border-40'"
            >
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">
                        {{ __('Order Number') }}
                    </h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="orderNumber"></p>
                </div>
            </div>
            <div class="flex" v-if="!hasError">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Total Items') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="itemCount"></p>
                </div>
            </div>
        </card>

        <card v-if="hasError">
            <div
                class="flex justify-center items-center text-50 px-6 py-8"
            >
                <div class="text-center">
                    <icon type="exclamation-outline" width="50" height="50" class="mb-3" />
                    <h3
                        class="text-base font-normal text-80 mb-2"
                    >
                        {{ __(this.errorMessage) }}
                    </h3>
                    <p class="text-base font-normal text-70">{{ __('Order status: ') }} {{ __(this.status) }}</p>
                </div>
            </div>
        </card>

        <div class="flex h-9 mb-6 items-center" v-if="! hasError">
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
                    @keydown.enter="itemPicked"
                    :disabled="this.counter == this.itemCount"
                />
            </div>

            <div class="ml-auto flex items-center mr-6">
                <span class="text-80 mr-3">{{ __('Items scanned') }}:</span>
                <heading :level="3">{{ counter }}</heading>
            </div>
        </div>

        <fetch-product-fields v-if="! hasError">
            <card slot-scope="{ fields, loading: loadingFields }">
                <table
                    v-show="list.length && !loadingFields"
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
                            <th class="text-left">{{ __('Location') }}</th>
                            <th class="text-center w-12">
                                {{ __('Quantity') }}
                            </th>
                            <th class="text-center w-12">{{ __('Picked') }}</th>
                            <th class="text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <fetch-product-information
                            v-for="(item, index) in list"
                            :key="`${index}-${item.gtin}`"
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
                                <td>{{ item.location }}</td>
                                <td class="text-center">{{ item.quantity }}</td>
                                <td class="text-center">{{ item.count }}</td>
                                <td class="text-center">
                                    <span
                                        v-if="item.quantity == item.count"
                                        class=" flex items-center text-success-dark"
                                    >
                                        <icon type="checkmark-outline" />
                                    </span>
                                </td>
                            </tr>
                        </fetch-product-information>
                    </tbody>
                </table>
            </card>
        </fetch-product-fields>
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
        orderId: {
            required: true,
        },
    },

    mounted() {
        this.initializeComponent();
    },

    data: () => ({
        counter: 0,
        gtin: '',
        initialLoading: true,
        itemCount: 0,
        list: [],
        orderNumber: '',
        status: '',
        errorMessage: '',
        hasError: false,
    }),

    methods: {
        async initializeComponent() {
            await this.getPickList();

            this.initialLoading = false;

            if (!this.list.length) {
                return;
            }

            this.setFocus();
        },

        async getPickList() {
            this.list = [];

            try {
                const {
                    data: { items, count, order_number, status },
                } = await Minimum(
                    Nova.request().get(
                        `/nova-vendor/mvdnbrk/warehouse-scan/orders/${this.orderId}/picklist`
                    )
                );

                this.itemCount = count;
                this.orderNumber = order_number;
                this.status = status;

                _.each(items, function(value) {
                    _.set(value, 'count', 0);
                });

                return (this.list = items);
            } catch (error) {
                if (error.response.status === 404) {
                    this.$router.push({ name: '404' });

                    return;
                }

                if (error.response.status === 422) {
                    this.orderNumber = error.response.data.order_number;
                    this.errorMessage = error.response.data.message;
                    this.status = error.response.data.status;
                    this.hasError = true;
                }
            }
        },

        itemPicked() {
            let listItem = _.find(this.list, item => {
                return item.gtin == this.gtin && item.count < item.quantity;
            });

            this.gtin = '';

            if (_.isUndefined(listItem)) {
                Nova.error(this.__('Wrong product scanned!'));

                return;
            }

            listItem.count++;
            this.counter++;

            if (this.counter == this.itemCount) {
                this.orderPicked();
            }
        },

        async orderPicked() {
            try {
                await Nova.request().post(
                    `/nova-vendor/mvdnbrk/warehouse-scan/orders/${this.orderId}/fulfilled`
                );

                Nova.success(this.__('Order picked successfully!'));

                if (
                    Nova.config.redirectAfterOrderFulfillement &&
                    this.orderNumber
                ) {
                    setTimeout(() => {
                        window.location.href =
                            Nova.config.redirectAfterOrderFulfillement +
                            '/' +
                            this.orderNumber;
                    }, 5000);
                }
            } catch (error) {
                Nova.error(this.__(error.response.data.message));
            }
        },

        setFocus() {
            this.$nextTick(() => {
                this.$refs.gtin.focus();
            });
        },
    },
};
</script>
