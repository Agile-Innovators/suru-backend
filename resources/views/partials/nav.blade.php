<?php
$links = array(
    array("name" => "Properties", "icon" => "icon-user", "route" => "properties.index"),
    array("name" => "Users", "icon" => "lucide-user", "route" => "users.index"),
    array("name" => "Appointments", "icon" => "lucide-calendar", "route" => "appointments.index"),
    array("name" => "Partners", "icon" => "lucide-users", "route" => "partners.index"),
);

if (session()->has('user')) {
    $links[] = array("name" => "Logout", "icon" => "lucide-log-out", "route" => "admin.logout");
}
?>

<nav class="flex flex-col gap-1 min-w-[240px] p-2 font-sans text-base font-normal text-gray-700">
    @foreach($links as $item)
        <a href="#">
            <div role="button" tabindex="0"
                class="flex items-center w-full p-3 rounded-lg text-start leading-tight transition-all hover:bg-blue-100 hover:bg-opacity-80 focus:bg-blue-50 focus:bg-opacity-80 active:bg-gray-50 active:bg-opacity-80 hover:text-blue-900 focus:text-blue-900 active:text-blue-900 outline-none">
                <div class="grid place-items-center mr-4">
                    <i class="{{ $item['icon'] }} h-5 w-5"></i> 
                </div>
                {{ $item['name'] }}
            </div>
        </a>
    @endforeach
</nav>

