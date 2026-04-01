<x-base-layout>
    <div class="d-flex">
        <div class="bg-dark text-white vh-100 p-3" style="width: 250px;">
            <h4>EduConnect</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link text-white">Dashboard</a></li>
                <li class="nav-item"><a href="" class="nav-link text-white">Colleges</a></li>
            </ul>
        </div>
        <div class="flex-grow-1 p-4">
            {{ $slot }}
        </div>
    </div>
</x-base-layout>