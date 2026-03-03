<x-admin>
    <x-slot:title>{{ $newsletter->subject }}</x-slot:title>
    
    <x-slot:header>
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes da Newsletter') }}
            </h2>
            <div class="flex space-x-2">
                @if(in_array($newsletter->status, ['draft', 'scheduled']))
                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}" 
                       class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    
                    @if($newsletter->status == 'scheduled' || $newsletter->status == 'draft')
                        <form action="{{ route('admin.newsletters.send', $newsletter) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Enviar newsletter agora para todos os assinantes?')">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar Agora
                            </button>
                        </form>
                    @endif
                @endif
                
                <a href="{{ route('admin.newsletters.index') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>
    </x-slot:header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                            <i class="fas fa-envelope text-white"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Status</div>
                            <div class="text-lg font-semibold">
                                @php
                                    $statusLabels = [
                                        'draft' => ['bg-gray-100', 'text-gray-800', 'Rascunho'],
                                        'scheduled' => ['bg-yellow-100', 'text-yellow-800', 'Agendada'],
                                        'sending' => ['bg-blue-100', 'text-blue-800', 'Enviando'],
                                        'sent' => ['bg-green-100', 'text-green-800', 'Enviada'],
                                        'cancelled' => ['bg-red-100', 'text-red-800', 'Cancelada'],
                                    ];
                                    $status = $statusLabels[$newsletter->status];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $status[0] }} {{ $status[1] }}">
                                    {{ $status[2] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Entregues</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['sent'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Pendentes</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Falhas</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['failed'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-envelope-open-text mr-2 text-blue-500"></i>
                            Conteúdo da Newsletter
                        </h3>
                        
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="mb-4">
                                <strong class="text-sm text-gray-600">Assunto:</strong>
                                <p class="mt-1 text-lg">{{ $newsletter->subject }}</p>
                            </div>
                            
                            <div>
                                <strong class="text-sm text-gray-600">Conteúdo:</strong>
                                <div class="mt-2 prose max-w-none">
                                    {!! $newsletter->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Informações
                        </h3>
                        
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado por</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $newsletter->creator->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $newsletter->created_at->format('d/m/Y H:i') }}
                                    <span class="text-xs text-gray-500">({{ $newsletter->created_at->diffForHumans() }})</span>
                                </dd>
                            </div>
                            
                            @if($newsletter->scheduled_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Agendado para</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $newsletter->scheduled_at->format('d/m/Y H:i') }}
                                    @if($newsletter->scheduled_at->isFuture())
                                        <span class="text-xs text-green-600">(futuro)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif
                            
                            @if($newsletter->sent_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Enviado em</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $newsletter->sent_at->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            @endif
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total de destinatários</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $stats['total'] }}</dd>
                            </div>
                            
                            @if($stats['total'] > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Taxa de sucesso</dt>
                                <dd class="mt-1">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 mr-2">
                                            {{ round(($stats['sent'] / $stats['total']) * 100, 1) }}%
                                        </span>
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-green-500 rounded-full" 
                                                 style="width: {{ ($stats['sent'] / $stats['total']) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Deliveries Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-paper-plane mr-2 text-blue-500"></i>
                        Registros de Envio
                    </h3>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Enviado em
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Erro
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($deliveries as $delivery)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $delivery->email }}</div>
                                        @if($delivery->user)
                                            <div class="text-xs text-gray-500">
                                                <i class="fas fa-user mr-1"></i>{{ $delivery->user->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => ['bg-yellow-100', 'text-yellow-800', 'fa-clock', 'Pendente'],
                                                'sent' => ['bg-green-100', 'text-green-800', 'fa-check-circle', 'Enviado'],
                                                'failed' => ['bg-red-100', 'text-red-800', 'fa-exclamation-circle', 'Falhou'],
                                            ];
                                            $status = $statusColors[$delivery->status];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status[0] }} {{ $status[1] }}">
                                            <i class="fas {{ $status[2] }} mr-1"></i>{{ $status[3] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $delivery->sent_at ? $delivery->sent_at->format('d/m/Y H:i:s') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($delivery->error_message)
                                            <div class="text-sm text-red-600" title="{{ $delivery->error_message }}">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ Str::limit($delivery->error_message, 50) }}
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Nenhum registro de envio encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $deliveries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin>