<nav class="bg-white justify-between w-full flex px-16 text-clr-white items-center shadow-md">
    <span>Suru</span>
    <section class="flex gap-4 duration-150">
        <a href="#" class="p-3 hover:bg-gray-300 h-full cursor-pointer">Properties</a>
        <a href="#" class="p-3 hover:bg-gray-300 h-full cursor-pointer">Users</a>
        <a href="#" class="p-3 hover:bg-gray-300 h-full cursor-pointer">Appointments</a>
        <a href="#" class="p-3 hover:bg-gray-300 h-full cursor-pointer">Partners</a>
        @if (session()->has('user'))
            <a href="{{ route('admin.logout') }}" class="p-3 hover:bg-gray-300 h-full cursor-pointer">Logout</a>
        @endif
    </section>
</nav>