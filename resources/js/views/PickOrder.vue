<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Pick Order') }}</heading>
        <card class="mb-6 py-3 px-6">
            <div class="flex border-b border-40">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Order Number') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="orderNumber"></p>
                </div>
            </div>
            <div class="flex">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Total Items') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="itemCount"></p>
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
                    @keydown.enter="itemPicked"
                    :disabled="this.counter == this.itemCount"
                />
            </div>

            <div class="ml-auto flex items-center mr-6">
                <span class="text-80 mr-3">{{ __('Items scanned') }}:</span>
                <heading :level="3">{{ counter }}</heading>
            </div>
        </div>

        <card>
            <table v-show="list.length" class="table w-full" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-left">{{ __('Location') }}</th>
                        <th class="text-left">GTIN</th>
                        <th class="text-center w-12">{{ __('Quantity') }}</th>
                        <th class="text-center w-12">{{ __('Picked') }}</th>
                        <th class="text-center w-12"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in list" :key="index">
                        <td>{{ item.location }}</td>
                        <td>{{ item.gtin }}</td>
                        <td class="text-center">{{ item.quantity }}</td>
                        <td class="text-center">{{ item.count }}</td>
                        <td class="text-center flex items-center">
                            <span v-if="item.quantity == item.count" class="text-success-dark">
                                <icon type="checkmark-outline" />
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
    }),

    methods: {
        async initializeComponent() {
            await this.getPickList();

            if (!this.list.length) {
                return;
            }

            this.initialLoading = false;

            this.setFocus();
        },

        async getPickList() {
            this.list = [];

            try {
                const {
                    data: { items, count, order_number },
                } = await Nova.request().get(
                    `/nova-vendor/mvdnbrk/warehouse-scan/orders/${this.orderId}/picklist`
                );

                this.itemCount = count;
                this.orderNumber = order_number;

                _.each(items, function(value) {
                    _.set(value, 'count', 0);
                });

                return (this.list = items);
            } catch (error) {
                if (error.response.status === 422 || error.response.status === 404) {
                    this.$router.push({ name: '404' });

                    return;
                }
            }
        },

        itemPicked() {
            let listItem = _.find(this.list, item => {
                return item.gtin == this.gtin && item.count < item.quantity;
            });

            this.gtin = '';

            if (_.isUndefined(listItem)) {
                this.$toasted.show(this.__('Wrong product scanned!'), { type: 'error' });

                return;
            }

            listItem.count++;
            this.counter++;

            if (this.counter == this.itemCount) {
                this.orderPicked();
            }
        },

        orderPicked() {
            Nova.request().post(
                `/nova-vendor/mvdnbrk/warehouse-scan/orders/${this.orderId}/fulfilled`
            );

            this.$toasted.show(this.__('Order picked successfully!'), { type: 'success' });
        },

        setFocus() {
            this.$nextTick(() => {
                this.$refs.gtin.focus();
            });
        },
    },
};
</script>
