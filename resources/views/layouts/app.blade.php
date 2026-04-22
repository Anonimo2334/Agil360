<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | Agil365 — Gestión de Proyectos Tecnológicos</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <style>
        /* ── Flatpickr custom theme: matches Agil365 design system ── */
        .flatpickr-calendar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            font-family: inherit;
            padding: 4px;
        }
        .dark .flatpickr-calendar {
            background: #1a2030;
            border-color: #2d3748;
            box-shadow: 0 10px 40px rgba(0,0,0,0.45);
        }
        .flatpickr-months {
            padding: 8px 4px 4px;
        }
        .flatpickr-month {
            color: #111827;
        }
        .dark .flatpickr-month,
        .dark .flatpickr-current-month,
        .dark .flatpickr-current-month .flatpickr-monthDropdown-months,
        .dark .flatpickr-current-month input.cur-year {
            color: #f1f5f9;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 600;
        }
        .flatpickr-prev-month svg,
        .flatpickr-next-month svg {
            fill: #6b7280;
        }
        .dark .flatpickr-prev-month svg,
        .dark .flatpickr-next-month svg {
            fill: #9ca3af;
        }
        .flatpickr-prev-month:hover svg,
        .flatpickr-next-month:hover svg {
            fill: #465fff;
        }
        .flatpickr-weekdays { padding: 4px 0; }
        .flatpickr-weekday {
            color: #9ca3af;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .dark .flatpickr-weekday { color: #6b7280; }
        .flatpickr-day {
            border-radius: 10px;
            color: #374151;
            font-size: 13px;
            transition: background 0.15s, color 0.15s;
            line-height: 36px;
            height: 36px;
        }
        .dark .flatpickr-day { color: #d1d5db; }
        .flatpickr-day:hover {
            background: #eef2ff;
            border-color: transparent;
            color: #465fff;
        }
        .dark .flatpickr-day:hover {
            background: rgba(70,95,255,0.15);
            color: #818cf8;
        }
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #465fff;
            border-color: #465fff;
            color: #fff !important;
        }
        .flatpickr-day.today {
            border-color: #465fff;
            color: #465fff;
            font-weight: 700;
        }
        .dark .flatpickr-day.today { border-color: #818cf8; color: #818cf8; }
        .flatpickr-day.today.selected { color: #fff !important; }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #d1d5db;
        }
        .dark .flatpickr-day.flatpickr-disabled,
        .dark .flatpickr-day.prevMonthDay,
        .dark .flatpickr-day.nextMonthDay {
            color: #374151;
        }
        /* Input wrapper icon reuse */
        .flatpickr-input { cursor: pointer; }
        .flatpickr-input:focus { outline: none; }

        /* ── Fix: calendar must appear ABOVE modals ── */
        .flatpickr-calendar {
            z-index: 99999999 !important;
        }
    </style>

    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Theme Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
                        'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark', 'bg-gray-900');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });

            Alpine.store('sidebar', {
                // Initialize based on screen size
                isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    // When toggling desktop sidebar, ensure mobile menu is closed
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                    // Don't modify isExpanded when toggling mobile menu
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    // Only allow hover effects on desktop when sidebar is collapsed
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (document.body) document.body.classList.add('dark', 'bg-gray-900');
            } else {
                document.documentElement.classList.remove('dark');
                if (document.body) document.body.classList.remove('dark', 'bg-gray-900');
            }
        })();
    </script>
    
</head>

<body
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @yield('content')
            </div>
        </div>

    </div>

    {{-- Global Modals Area --}}
    @stack('modals')
</body>

@stack('scripts')

<!-- ── Global Flatpickr initializer ─────────────────────────────────────── -->
<script>
(function () {
    // Spanish locale is loaded from the l10n CDN above
    const locale = (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.es)
        ? flatpickr.l10ns.es
        : 'es';

    const isDark = () => document.documentElement.classList.contains('dark');

    function initDatePickers() {
        // Select all date inputs that haven't been initialized yet
        document.querySelectorAll('input[type="date"]:not(.flatpickr-input)').forEach(function (el) {
            // Preserve any existing value
            const existingValue = el.value;

            flatpickr(el, {
                locale: locale,
                dateFormat: 'Y-m-d',          // keeps server-compatible format
                altInput: true,               // shows human-friendly format
                altFormat: 'd/m/Y',           // e.g. 19/04/2026
                allowInput: true,
                disableMobile: false,
                position: 'auto',
                appendTo: document.body,      // render outside modals / overflow containers
                defaultDate: existingValue || null,
                onReady: function (_, __, instance) {
                    // Style the alt input to match the existing form inputs
                    const altEl = instance.altInput;
                    // Copy classes from original, remove type=date for styling
                    altEl.className = el.className
                        .replace('flatpickr-input', '')
                        .trim();
                    altEl.placeholder = el.placeholder || 'dd/mm/aaaa';
                }
            });
        });
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDatePickers);
    } else {
        initDatePickers();
    }

    // Re-run when modals open (MutationObserver picks up dynamically added inputs)
    const observer = new MutationObserver(function (mutations) {
        let shouldInit = false;
        mutations.forEach(function (m) {
            if (m.addedNodes.length) shouldInit = true;
        });
        if (shouldInit) initDatePickers();
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Re-init when dark mode toggles so calendar stays themed
    const htmlEl = document.documentElement;
    const themeObserver = new MutationObserver(function () {
        document.querySelectorAll('.flatpickr-calendar').forEach(function (cal) {
            if (isDark()) {
                cal.classList.add('dark');
            } else {
                cal.classList.remove('dark');
            }
        });
    });
    themeObserver.observe(htmlEl, { attributes: true, attributeFilter: ['class'] });
})();
</script>

</html>
