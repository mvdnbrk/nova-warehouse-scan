<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path fill="var(--sidebar-icon)" d="M2 0C.9 0 0 .9 0 2v16c0 1.1.9 2 2 2V0zM3 0h2v20H3zM6 0h2v20H6zM9 0h5v20H9zM15 0h2v20h-2zM18 0v20c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2z"/>
    </svg>
    <span class="sidebar-label">Scan</span>
</h3>

<ul class="list-reset mb-8">
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'locations.add-inventory.index'}" class="text-white text-justify no-underline dim">
            Add Inventory
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'locations.move-inventory.index'}" class="text-white text-justify no-underline dim">
            Move Inventory
        </router-link>
    </li>
    <li class="leading-tight mb-4 ml-8 text-sm">
        <router-link :to="{name: 'orders.pick.index'}" class="text-white text-justify no-underline dim">
            Pick Orders
        </router-link>
    </li>
</ul>
