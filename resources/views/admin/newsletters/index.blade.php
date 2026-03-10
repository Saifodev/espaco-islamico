{{-- resources/views/admin/newsletters/index.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">Newsletters</h2>
                <p class="text-sm text-gray-600 mt-1">Gerencie o conteúdo enviado aos assinantes</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('admin.newsletters.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Newsletter
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Total</p>
                    <p class="mt-1 text-xl sm:text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Rascunhos</p>
                    <p class="mt-1 text-xl sm:text-3xl font-semibold text-gray-900">{{ $stats['drafts'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Agendadas</p>
                    <p class="mt-1 text-xl sm:text-3xl font-semibold text-gray-900">{{ $stats['scheduled'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Enviadas</p>
                    <p class="mt-1 text-xl sm:text-3xl font-semibold text-gray-900">{{ $stats['sent'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 col-span-2 md:col-span-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Assinantes</p>
                    <p class="mt-1 text-xl sm:text-3xl font-semibold text-gray-900">{{ $stats['subscribers'] }}</p>
                    <a href="{{ route('admin.newsletters.subscribers') }}" class="text-xs text-green-600 hover:text-green-700 font-medium inline-block mt-1">
                        Gerenciar <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Filtros (Opcional, pode ser adicionado se houver necessidade) -->
            {{-- <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Assunto..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                        </div>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-filter mr-2"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.newsletters.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div> --}}

            <!-- Lista de Newsletters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                @if(session('success'))
                    <div class="m-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assunto</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Criado por</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Agendamento</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($newsletters as $newsletter)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $newsletter->subject }}</div>
                                        <div class="text-xs text-gray-500 md:hidden mt-1">
                                            Por: {{ $newsletter->creator->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-gray-100 text-gray-700',
                                                'scheduled' => 'bg-yellow-100 text-yellow-700',
                                                'sending' => 'bg-blue-100 text-blue-700',
                                                'sent' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusIcons = [
                                                'draft' => 'fa-pen',
                                                'scheduled' => 'fa-clock',
                                                'sending' => 'fa-paper-plane',
                                                'sent' => 'fa-check-circle',
                                                'cancelled' => 'fa-times-circle',
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Rascunho',
                                                'scheduled' => 'Agendada',
                                                'sending' => 'Enviando',
                                                'sent' => 'Enviada',
                                                'cancelled' => 'Cancelada',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$newsletter->status] }}">
                                            <i class="fas {{ $statusIcons[$newsletter->status] }} mr-1.5"></i>
                                            {{ $statusLabels[$newsletter->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-gray-900">{{ $newsletter->creator->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                        @if($newsletter->scheduled_at)
                                            <div class="text-sm text-gray-900">
                                                {{ $newsletter->scheduled_at->format('d/m/Y H:i') }}
                                            </div>
                                        @elseif($newsletter->sent_at)
                                            <div class="text-sm text-gray-500">
                                                Enviada: {{ $newsletter->sent_at->format('d/m/Y H:i') }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.newsletters.show', $newsletter) }}"
                                               class="p-2 text-gray-500 hover:text-blue-600 transition-colors"
                                               title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(in_array($newsletter->status, ['draft', 'scheduled']))
                                                <a href="{{ route('admin.newsletters.edit', $newsletter) }}"
                                                   class="p-2 text-gray-500 hover:text-green-600 transition-colors"
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if($newsletter->status == 'scheduled' || $newsletter->status == 'draft')
                                                    <form action="{{ route('admin.newsletters.send', $newsletter) }}"
                                                          method="POST" class="inline"
                                                          onsubmit="return confirm('Enviar newsletter agora para todos os assinantes?')">
                                                        @csrf
                                                        <button type="submit"
                                                                class="p-2 text-gray-500 hover:text-yellow-600 transition-colors"
                                                                title="Enviar agora">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($newsletter->status == 'draft')
                                                    <form action="{{ route('admin.newsletters.destroy', $newsletter) }}"
                                                          method="POST" class="inline"
                                                          onsubmit="return confirm('Excluir este rascunho?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="p-2 text-gray-500 hover:text-red-600 transition-colors"
                                                                title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-sm">Nenhuma newsletter encontrada</p>
                                        <a href="{{ route('admin.newsletters.create') }}"
                                           class="inline-flex items-center mt-3 text-sm text-green-600 hover:text-green-700">
                                            <i class="fas fa-plus mr-1"></i>
                                            Criar nova newsletter
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($newsletters->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $newsletters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin>