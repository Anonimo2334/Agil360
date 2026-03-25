{{-- Notification Dropdown Component --}}
<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    @php
        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->hasAnyRole(['super_admin', 'admin', 'gerente']))) {
            $navAlerts = \App\Models\Alert::with('project')
                ->where('is_read', false)
                ->where('status', 'activa')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            $unreadCount = \App\Models\Alert::where('is_read', false)->where('status', 'activa')->count();
        } elseif ($user) {
            $myProjectIds = \App\Models\Project::where('primary_engineer_id', $user->id)
                ->orWhere('backup_engineer_id', $user->id)
                ->pluck('id');
                
            $navAlerts = \App\Models\Alert::with('project')
                ->whereIn('project_id', $myProjectIds)
                ->where('is_read', false)
                ->where('status', 'activa')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            $unreadCount = \App\Models\Alert::whereIn('project_id', $myProjectIds)->where('is_read', false)->where('status', 'activa')->count();
        } else {
            $navAlerts = collect();
            $unreadCount = 0;
        }
    @endphp

    <!-- Notification Button -->
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()"
        type="button"
    >
        <!-- Notification Badge -->
        @if($unreadCount > 0)
        <span class="absolute right-0 top-0.5 z-1 flex h-2.5 w-2.5 items-center justify-center">
            <span class="absolute inline-flex w-full h-full bg-error-500 rounded-full opacity-75 animate-ping"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-error-500"></span>
        </span>
        @endif

        <!-- Bell Icon -->
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill=""/>
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div
        x-show="dropdownOpen"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-auto max-h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:w-[361px] lg:right-0"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h5 class="text-base font-semibold text-gray-800 dark:text-white/90">Notificaciones</h5>
                @if($unreadCount > 0)
                <p class="text-xs text-brand-500">{{ $unreadCount }} sin leer</p>
                @endif
            </div>

            <button @click="closeDropdown()" class="p-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors" type="button">
                <svg class="fill-current w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill=""/>
                </svg>
            </button>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
            @forelse ($navAlerts as $notification)
                <li>
                    <a href="{{ route('alertas') }}" class="flex gap-3 rounded-lg border-b border-gray-100 p-3 px-4.5 py-3 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5 transition-colors">
                        <div class="relative flex items-center justify-center w-10 h-10 rounded-full flex-shrink-0 {{ $notification->severity === 'error' ? 'bg-error-100 text-error-500 dark:bg-error-500/20' : 'bg-warning-100 text-warning-500 dark:bg-warning-500/20' }}">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
                                <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span class="absolute bottom-0 right-0 z-10 block h-2.5 w-2.5 rounded-full ring-2 ring-white dark:ring-gray-900 {{ $notification->severity === 'error' ? 'bg-error-500' : 'bg-warning-500' }}"></span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $notification->project->project_name ?? 'Sistema' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $notification->message }}</p>
                            <p class="text-[10px] text-gray-400 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                </li>
            @empty
                <li class="py-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-success-50 dark:bg-success-500/10 flex items-center justify-center mx-auto mb-2 text-success-500">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Todo al día</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No hay alertas nuevas en<br>este momento.</p>
                </li>
            @endforelse
        </ul>

        <!-- View All Button -->
        <a href="{{ route('alertas') }}" @click="closeDropdown()" class="mt-3 flex justify-center rounded-lg border border-gray-200 bg-gray-50 p-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
            Ver todas las alertas
        </a>
    </div>
    <!-- Dropdown End -->
</div>
