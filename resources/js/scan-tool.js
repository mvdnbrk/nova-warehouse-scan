import IconScan from './components/icons/Scan.vue';
import IconCloseOutline from './components/icons/CloseOutline.vue';
import IconCheckmarkOutline from './components/icons/CheckmarkOutline.vue';

Nova.booting((Vue, router) => {
    Vue.component('icon-scan', IconScan);
    Vue.component('icon-close-outline', IconCloseOutline);
    Vue.component('icon-checkmark-outline', IconCheckmarkOutline);

    router.addRoutes([
    ]);
});
