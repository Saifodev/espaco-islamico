{{-- resources/views/dashboard.blade.php --}}
<x-admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-circle text-4xl text-blue-600"></i>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold text-gray-800">
                                Bem-vindo, {{ Auth::user()->name_capitalized }}!
                            </h3>
                            <p class="text-gray-600 mt-1">
                                {{ __('You\'re logged in as') }} 
                                <span class="font-semibold text-blue-600">
                                    @foreach(Auth::user()->roles as $role)
                                        {{ ucfirst($role->name) }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Users -->
                @can('view users')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">
                                    Total de Usuários
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Active Users -->
                @can('view users')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-user-check text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">
                                    Usuários Ativos
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::active()->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- Pending Invites -->
                @can('view users')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-envelope text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">
                                    Convites Pendentes
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ \App\Models\User::whereNotNull('invitation_sent_at')->whereNull('invitation_accepted_at')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <!-- System Status -->
                @can('access dev panel')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-server text-white text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">
                                    Status do Sistema
                                </p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    <span class="text-green-600">●</span> Online
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endcan
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @can('create users')
                        <a href="{{ route('admin.users.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition duration-150">
                            <i class="fas fa-user-plus text-2xl text-blue-600 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Novo Usuário</p>
                        </a>
                        @endcan

                        @can('view users')
                        <a href="{{ route('admin.users.index') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition duration-150">
                            <i class="fas fa-list text-2xl text-green-600 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Gerenciar Usuários</p>
                        </a>
                        @endcan

                        {{-- @can('access dev panel')
                        <a href="{{ route('dev.logs') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition duration-150">
                            <i class="fas fa-chart-line text-2xl text-purple-600 mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Logs do Sistema</p>
                        </a>
                        @endcan --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>