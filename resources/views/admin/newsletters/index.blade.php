{{-- resources/views/admin/newsletters/index.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Newsletters') }}
            </h2>
            <a href="{{ route('admin.newsletters.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Nova Newsletter
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Total</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Rascunhos</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['drafts'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Agendadas</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['scheduled'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Enviadas</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['sent'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Assinantes</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['subscribers'] }}</div>
                    <a href="{{ route('admin.newsletters.subscribers') }}" class="text-sm text-blue-600 hover:text-blue-900">
                        Gerenciar →
                    </a>
                </div>
            </div>

            <!-- Lista de Newsletters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assunto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Criado por
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Agendamento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($newsletters as $newsletter)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $newsletter->subject }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'draft' => 'bg-gray-100 text-gray-800',
                                                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                                                    'sending' => 'bg-blue-100 text-blue-800',
                                                    'sent' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$newsletter->status] }}">
                                                @switch($newsletter->status)
                                                    @case('draft') Rascunho @break
                                                    @case('scheduled') Agendada @break
                                                    @case('sending') Enviando @break
                                                    @case('sent') Enviada @break
                                                    @case('cancelled') Cancelada @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $newsletter->creator->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($newsletter->scheduled_at)
                                                <div class="text-sm text-gray-900">
                                                    {{ $newsletter->scheduled_at->format('d/m/Y H:i') }}
                                                </div>
                                            @elseif($newsletter->sent_at)
                                                <div class="text-sm text-gray-500">
                                                    Enviada: {{ $newsletter->sent_at->format('d/m/Y H:i') }}
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.newsletters.show', $newsletter) }}" 
                                                   class="text-blue-600 hover:text-blue-900" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(in_array($newsletter->status, ['draft', 'scheduled']))
                                                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}" 
                                                       class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    @if($newsletter->status == 'scheduled' || $newsletter->status == 'draft')
                                                        <form action="{{ route('admin.newsletters.send', $newsletter) }}" 
                                                              method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="text-green-600 hover:text-green-900" 
                                                                    title="Enviar agora"
                                                                    onclick="return confirm('Enviar newsletter agora para todos os assinantes?')">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    
                                                    @if($newsletter->status == 'draft')
                                                        <form action="{{ route('admin.newsletters.destroy', $newsletter) }}" 
                                                              method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="text-red-600 hover:text-red-900" 
                                                                    title="Excluir"
                                                                    onclick="return confirm('Excluir este rascunho?')">
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
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Nenhuma newsletter encontrada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $newsletters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>