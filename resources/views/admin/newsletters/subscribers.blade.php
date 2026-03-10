{{-- resources/views/admin/newsletters/subscribers.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">Assinantes da Newsletter</h2>
                <p class="text-sm text-gray-600 mt-1">Gerencie quem recebe suas newsletters</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('admin.newsletters.subscribers.export') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Exportar CSV
                </a>
                <a href="{{ route('admin.newsletters.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total de Assinantes</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $subscribers->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ativos</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                {{ App\Models\NewsletterSubscriber::active()->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 col-span-1 sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4 min-w-0">
                            <p class="text-sm font-medium text-gray-500 truncate">Última inscrição</p>
                            <p class="text-lg font-semibold text-gray-900 truncate">
                                @php
                                    $last = App\Models\NewsletterSubscriber::latest()->first();
                                @endphp
                                {{ $last ? $last->created_at->diffForHumans() : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscribers Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <!-- Search -->
                    <form method="GET" class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Buscar por email ou nome..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 sm:flex-none px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-search sm:mr-2"></i>
                                <span class="hidden sm:inline">Buscar</span>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.newsletters.subscribers') }}"
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Nome</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Inscrito em</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($subscribers as $subscriber)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $subscriber->email }}</div>
                                        @if($subscriber->name)
                                            <div class="text-xs text-gray-500 sm:hidden mt-1">{{ $subscriber->name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <div class="text-sm text-gray-900">
                                            {{ $subscriber->name ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($subscriber->is_active)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-check-circle mr-1.5"></i>
                                                Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-times-circle mr-1.5"></i>
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-gray-900">
                                            {{ $subscriber->created_at->format('d/m/Y H:i') }}
                                        </div>
                                        @if($subscriber->unsubscribed_at)
                                            <div class="text-xs text-gray-500">
                                                Cancelou: {{ $subscriber->unsubscribed_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <form action="{{ route('admin.newsletters.subscribers.destroy', $subscriber) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Tem certeza que deseja remover este assinante?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-gray-500 hover:text-red-600 transition-colors"
                                                    title="Remover">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <i class="fas fa-users-slash text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-sm">Nenhum assinante encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($subscribers->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $subscribers->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin>