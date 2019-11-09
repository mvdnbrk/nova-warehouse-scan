import IconScan from './components/icons/Scan.vue';
import IconCloseOutline from './components/icons/CloseOutline.vue';
import IconCheckmarkOutline from './components/icons/CheckmarkOutline.vue';
import IconExclamationOutline from './components/icons/ExclamationOutline.vue';

import ChooseLocation from './components/ChooseLocation.vue';
import ConfirmNewLocationModal from './components/modals/ConfirmNewLocationModal.vue';

import AddInventory from './views/AddInventory.vue';
import AddInventoryIndex from './views/AddInventoryIndex.vue';
import MoveInventory from './views/MoveInventory.vue';
import MoveInventoryIndex from './views/MoveInventoryIndex.vue';

import PickOrderIndex from './views/PickOrderIndex.vue';
import PickOrder from './views/PickOrder.vue';

Nova.booting((Vue, router) => {
    Vue.component('icon-scan', IconScan);
    Vue.component('icon-close-outline', IconCloseOutline);
    Vue.component('icon-checkmark-outline', IconCheckmarkOutline);
    Vue.component('icon-exclamation-outline', IconExclamationOutline);
    Vue.component('choose-location', ChooseLocation);
    Vue.component('confirm-new-location-modal', ConfirmNewLocationModal);

    router.addRoutes([
        {
            name: 'locations.add-inventory.index',
            path: '/scan/add-inventory',
            component: AddInventoryIndex,
        },
        {
            name: 'locations.add-inventory',
            path: '/scan/add-inventory/:locationId',
            component: AddInventory,
            props: true,
        },
        {
            name: 'locations.move-inventory.index',
            path: '/scan/move-inventory',
            component: MoveInventoryIndex,
        },
        {
            name: 'locations.move-inventory',
            path: '/scan/move-inventory/:locationId',
            component: MoveInventory,
            props: true,
        },
        {
            name: 'orders.pick.index',
            path: '/scan/pick-order',
            component: PickOrderIndex,
        },
        {
            name: 'orders.pick',
            path: '/scan/pick-order/:orderId',
            component: PickOrder,
            props: true,
        },
    ]);
});
