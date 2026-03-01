{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('manage users')
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Usuários') }}
                        </x-nav-link>
                    @endcan

                    {{-- @can('manage roles')
                        <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                            {{ __('Perfis') }}
                        </x-nav-link>
                    @endcan

                    @can('create articles')
                        <x-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')">
                            {{ __('Artigos') }}
                        </x-nav-link>
                    @endcan

                    @can('access dev panel')
                        <x-nav-link :href="route('dev.dashboard')" :active="request()->routeIs('dev.*')">
                            {{ __('Dev Panel') }}
                        </x-nav-link>
                    @endcan --}}
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @if(session()->has('impersonate'))
                    <div class="mr-4">
                        <form action="{{ route('admin.users.leave-impersonate') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1 px-3 rounded-full">
                                <i class="fas fa-mask mr-1"></i>Sair do modo impersonate
                            </button>
                        </form>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                @if(session()->has('impersonate'))
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                        Impersonating
                                    </span>
                                @endif
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- User Info (visible only in dropdown) -->
                        <div class="px-4 py-3 border-b border-gray-200">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ Auth::user()->name_capitalized }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ Auth::user()->email }}
                            </p>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach(Auth::user()->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <x-dropdown-link :href="route('admin.profile.edit')">
                            <i class="fas fa-user mr-2"></i>{{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- @can('access dev panel')
                            <x-dropdown-link :href="route('dev.logs')">
                                <i class="fas fa-terminal mr-2"></i>{{ __('System Logs') }}
                            </x-dropdown-link>
                        @endcan --}}

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                <i class="fas fa-tachometer-alt mr-2"></i>{{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('manage users')
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <i class="fas fa-users mr-2"></i>{{ __('Usuários') }}
                </x-responsive-nav-link>
            @endcan

            {{-- @can('manage roles')
                <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                    <i class="fas fa-user-tag mr-2"></i>{{ __('Perfis') }}
                </x-responsive-nav-link>
            @endcan

            @can('create articles')
                <x-responsive-nav-link :href="route('admin.articles.index')" :active="request()->routeIs('admin.articles.*')">
                    <i class="fas fa-newspaper mr-2"></i>{{ __('Artigos') }}
                </x-responsive-nav-link>
            @endcan

            @can('access dev panel')
                <x-responsive-nav-link :href="route('dev.dashboard')" :active="request()->routeIs('dev.*')">
                    <i class="fas fa-code mr-2"></i>{{ __('Dev Panel') }}
                </x-responsive-nav-link>
            @endcan --}}
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach(Auth::user()->roles as $role)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ ucfirst($role->name) }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if(session()->has('impersonate'))
                <div class="mt-3 px-4">
                    <form action="{{ route('admin.users.leave-impersonate') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left bg-yellow-100 hover:bg-yellow-200 text-yellow-800 text-sm font-medium py-2 px-3 rounded-md">
                            <i class="fas fa-mask mr-2"></i>Sair do modo impersonate
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('admin.profile.edit')">
                    <i class="fas fa-user mr-2"></i>{{ __('Profile') }}
                </x-responsive-nav-link>

                {{-- @can('access dev panel')
                    <x-responsive-nav-link :href="route('dev.logs')">
                        <i class="fas fa-terminal mr-2"></i>{{ __('System Logs') }}
                    </x-responsive-nav-link>
                @endcan --}}

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>