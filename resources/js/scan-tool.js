import IconScan from './components/icons/Scan.vue';
import IconCloseOutline from './components/icons/CloseOutline.vue';
import IconCheckmarkOutline from './components/icons/CheckmarkOutline.vue';

import ChooseLocation from './components/ChooseLocation.vue';
import AddInventory from './views/AddInventory.vue';
import AddInventoryIndex from './views/AddInventoryIndex.vue';
import MoveInventory from './views/MoveInventory.vue';
import MoveInventoryIndex from './views/MoveInventoryIndex.vue';

Nova.booting((Vue, router) => {
    Vue.component('icon-scan', IconScan);
    Vue.component('icon-close-outline', IconCloseOutline);
    Vue.component('icon-checkmark-outline', IconCheckmarkOutline);
    Vue.component('choose-location', ChooseLocation);

    router.addRoutes([
    ]);
});
