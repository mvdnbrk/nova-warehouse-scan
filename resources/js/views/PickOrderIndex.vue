<template>
    <loading-view :loading="initialLoading">
        <heading class="mb-3">{{ __('Pick Orders') }}</heading>
        <card class="mb-6 py-3 px-6">
            <div class="flex">
                <div class="w-1/4 py-4">
                    <h4 class="font-normal text-80">{{ __('Open orders') }}</h4>
                </div>
                <div class="w-3/4 py-4">
                    <p class="text-90" v-text="count"></p>
                </div>
            </div>
        </card>

        <card>
            <table
                v-show="items.length"
                class="table w-full"
                cellpadding="0"
                cellspacing="0"
            >
                <thead>
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">{{ __('Order Number') }}</th>
                        <th class="text-left">{{ __('Created At') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items" :key="index">
                        <td>{{ item.id }}</td>
                        <td>{{ item.order_number }}</td>
                        <td>
                            <index-date-time
                                :field="{
                                    value: item.created_at,
                                    textAlign: 'left',
                                }"
                            />
                        </td>
                        <td class="td-fit text-right pr-6">
                            <router-link
                                class="cursor-pointer text-70 hover:text-primary mr-3"
                                :to="{
                                    name: 'orders.pick',
                                    params: {
                                        orderId: item.id,
                                    },
                                }"
                                :title="__('View')"
                            >
                                <icon
                                    type="view"
                                    width="22"
                                    height="18"
                                    view-box="0 0 22 16"
                                />
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </card>
    </loading-view>
</template>

<script>
import { Minimum } from 'laravel-nova';

export default {
    mounted() {
        this.initializeComponent();
    },

    data: () => ({
        count: 0,
        items: [],
        initialLoading: true,
    }),

    methods: {
        async initializeComponent() {
            const {
                data: { items, count },
            } = await Minimum(
                Nova.request().get(
                    `/nova-vendor/mvdnbrk/warehouse-scan/orders/open`
                )
            );

            this.count = count;
            this.items = items;
            this.initialLoading = false;
        },
    },
};
</script>
