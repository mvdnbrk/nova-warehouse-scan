import Tool from './components/Tool.vue';

Nova.booting((Vue, router) => {
    router.addRoutes([
        {
            name: 'nova-warehouse-scan',
            path: '/nova-warehouse-scan',
            component: Tool,
        },
    ]);
});
