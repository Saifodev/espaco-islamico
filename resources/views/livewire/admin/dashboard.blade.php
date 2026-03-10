{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8" x-data="{ abasAtiva: 'visao-geral', mostrarFiltros: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabeçalho -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-serif font-bold text-gray-900">Dashboard Editorial</h1>
                <p class="text-gray-600 mt-1">Acompanhe o desempenho do portal em tempo real</p>
            </div>

            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <!-- Seletor de período -->
                <div class="relative">
                    <button @click="mostrarFiltros = !mostrarFiltros"
                        class="flex items-center space-x-2 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                        <span>
                            @switch($periodo)
                                @case('7_dias')
                                    Últimos 7 dias
                                @break

                                @case('30_dias')
                                    Últimos 30 dias
                                @break

                                @case('90_dias')
                                    Últimos 90 dias
                                @break

                                @default
                                    Período personalizado
                            @endswitch
                        </span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <div x-show="mostrarFiltros" @click.away="mostrarFiltros = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                        <div class="py-1">
                            <button wire:click="$set('periodo', '7_dias')" @click="mostrarFiltros = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Últimos 7 dias
                            </button>
                            <button wire:click="$set('periodo', '30_dias')" @click="mostrarFiltros = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Últimos 30 dias
                            </button>
                            <button wire:click="$set('periodo', '90_dias')" @click="mostrarFiltros = false"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Últimos 90 dias
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botão atualizar -->
                <button wire:click="$refresh"
                    class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Atualizar
                </button>
            </div>
        </div>

        <!-- Cards de boas-vindas -->
        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-2xl p-6 mb-8 border border-green-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <i class="fas fa-user-circle text-3xl text-green-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-serif font-semibold text-gray-800">
                            Bem-vindo(a), {{ Auth::user()->name }}
                        </h2>
                        <p class="text-green-700 text-sm mt-1">
                            <i class="fas fa-circle text-xs mr-2"></i>
                            {{ Auth::user()->roles->pluck('name')->map('ucfirst')->join(', ') }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 md:mt-0 flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <span>Último acesso:
                            {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'Primeiro acesso' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abas de navegação -->
        <div class="border-b border-gray-200 mb-8 overflow-x-auto">
            <nav class="flex space-x-8 min-w-max">
                <button @click="abasAtiva = 'visao-geral'"
                    :class="{ 'border-green-600 text-green-600': abasAtiva === 'visao-geral', 'border-transparent text-gray-500': abasAtiva !== 'visao-geral' }"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fas fa-chart-pie mr-2"></i> Visão Geral
                </button>
                <button @click="abasAtiva = 'conteudo'"
                    :class="{ 'border-green-600 text-green-600': abasAtiva === 'conteudo', 'border-transparent text-gray-500': abasAtiva !== 'conteudo' }"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fas fa-newspaper mr-2"></i> Conteúdo
                </button>
                <button @click="abasAtiva = 'comunidade'"
                    :class="{ 'border-green-600 text-green-600': abasAtiva === 'comunidade', 'border-transparent text-gray-500': abasAtiva !== 'comunidade' }"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i> Comunidade
                </button>
                <button @click="abasAtiva = 'analytics'"
                    :class="{ 'border-green-600 text-green-600': abasAtiva === 'analytics', 'border-transparent text-gray-500': abasAtiva !== 'analytics' }"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fas fa-chart-line mr-2"></i> Analytics
                </button>
            </nav>
        </div>

        <!-- Conteúdo das abas -->
        <div x-show="abasAtiva === 'visao-geral'" x-transition>
            @include('livewire.admin.dashboard.overview')
        </div>

        <div x-show="abasAtiva === 'conteudo'" x-transition x-cloak>
            @include('livewire.admin.dashboard.content')
        </div>

        <div x-show="abasAtiva === 'comunidade'" x-transition x-cloak>
            @include('livewire.admin.dashboard.community')
        </div>

        <div x-show="abasAtiva === 'analytics'" x-transition x-cloak>
            @include('livewire.admin.dashboard.analytics')
        </div>

        <!-- Loading state -->
        <div wire:loading.flex class="fixed inset-0 bg-white bg-opacity-75 z-50 items-center justify-center"
            style="display: none;">
            <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span>Atualizando dados...</span>
            </div>
        </div>

        <!-- Notificações -->
        <div x-data="{ notificacao: { mostrar: false, mensagem: '' } }"
            x-on:notificacao.window="notificacao.mostrar = true; notificacao.mensagem = $event.detail[0]; setTimeout(() => notificacao.mostrar = false, 3000)"
            class="fixed bottom-4 right-4 z-50">
            <div x-show="notificacao.mostrar" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg">
                <i class="fas fa-check-circle mr-2"></i>
                <span x-text="notificacao.mensagem"></span>
            </div>
        </div>
    </div>
</div>