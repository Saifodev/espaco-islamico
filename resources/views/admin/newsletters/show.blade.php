{{-- resources/views/admin/newsletters/show.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">{{ $newsletter->subject }}</h2>
                <p class="text-sm text-gray-600 mt-1">Detalhes completos da newsletter</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                @if(in_array($newsletter->status, ['draft', 'scheduled']))
                    <a href="{{ route('admin.newsletters.edit', $newsletter) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Editar
                    </a>

                    @if($newsletter->status == 'scheduled' || $newsletter->status == 'draft')
                        <form action="{{ route('admin.newsletters.send', $newsletter) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Enviar newsletter agora para todos os assinantes?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Agora
                            </button>
                        </form>
                    @endif
                @endif
                <a href="{{ redirect()->back()->getTargetUrl() }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Status Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                            <i class="fas fa-envelope text-blue-600"></i>
                        </div>
                        <div class="ml-4 min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase truncate">Status</p>
                            @php
                                $statusClasses = [
                                    'draft' => ['bg-gray-100', 'text-gray-800', 'fa-pen'],
                                    'scheduled' => ['bg-yellow-100', 'text-yellow-800', 'fa-clock'],
                                    'sending' => ['bg-blue-100', 'text-blue-800', 'fa-paper-plane'],
                                    'sent' => ['bg-green-100', 'text-green-800', 'fa-check-circle'],
                                    'cancelled' => ['bg-red-100', 'text-red-800', 'fa-times-circle'],
                                ];
                                $status = $statusClasses[$newsletter->status];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1 {{ $status[0] }} {{ $status[1] }}">
                                <i class="fas {{ $status[2] }} mr-1"></i>
                                @lang('statuses.' . $newsletter->status)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase">Entregues</p>
                            <p class="text-xl sm:text-2xl font-semibold text-gray-900 mt-1">{{ $stats['sent'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase">Pendentes</p>
                            <p class="text-xl sm:text-2xl font-semibold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-medium text-gray-500 uppercase">Falhas</p>
                            <p class="text-xl sm:text-2xl font-semibold text-gray-900 mt-1">{{ $stats['failed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Details Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-envelope-open-text text-green-600 mr-2"></i>
                            Conteúdo da Newsletter
                        </h4>
                        <div class="border rounded-lg p-6 bg-gray-50">
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <span class="text-xs font-medium text-gray-500 uppercase">Assunto</span>
                                <p class="mt-1 text-lg font-medium text-gray-900">{{ $newsletter->subject }}</p>
                            </div>
                            <div>
                                <span class="text-xs font-medium text-gray-500 uppercase">Conteúdo</span>
                                <div class="mt-3 prose prose-sm sm:prose max-w-none">
                                    {!! $newsletter->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-green-600 mr-2"></i>
                            Informações
                        </h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Criado por</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $newsletter->creator->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Criado em</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $newsletter->created_at->format('d/m/Y H:i') }}
                                    <span class="text-xs text-gray-500 block sm:inline sm:ml-1">({{ $newsletter->created_at->diffForHumans() }})</span>
                                </dd>
                            </div>

                            @if($newsletter->scheduled_at)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Agendado para</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $newsletter->scheduled_at->format('d/m/Y H:i') }}
                                    @if($newsletter->scheduled_at->isFuture())
                                        <span class="text-xs text-green-600 ml-1">(futuro)</span>
                                    @endif
                                </dd>
                            </div>
                            @endif

                            @if($newsletter->sent_at)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Enviado em</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $newsletter->sent_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            @endif

                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Total de destinatários</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">{{ $stats['total'] }}</dd>
                            </div>

                            @if($stats['total'] > 0)
                            <div>
                                <dt class="text-xs text-gray-500 uppercase mb-2">Taxa de sucesso</dt>
                                <dd>
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 mr-3">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h4 class="text-lg font-serif font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-paper-plane text-green-600 mr-2"></i>
                        Registros de Envio
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Enviado em</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Erro</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($deliveries as $delivery)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $delivery->email }}</div>
                                        @if($delivery->user)
                                            <div class="text-xs text-gray-500 flex items-center mt-1 md:hidden">
                                                <i class="fas fa-user mr-1"></i>{{ $delivery->user->name }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $deliveryStatusClasses = [
                                                'pending' => ['bg-yellow-100', 'text-yellow-700', 'fa-clock'],
                                                'sent' => ['bg-green-100', 'text-green-700', 'fa-check-circle'],
                                                'failed' => ['bg-red-100', 'text-red-700', 'fa-exclamation-circle'],
                                            ];
                                            $deliveryStatus = $deliveryStatusClasses[$delivery->status];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $deliveryStatus[0] }} {{ $deliveryStatus[1] }}">
                                            <i class="fas {{ $deliveryStatus[2] }} mr-1.5"></i>
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <div class="text-sm text-gray-900">
                                            {{ $delivery->sent_at ? $delivery->sent_at->format('d/m/Y H:i:s') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 hidden lg:table-cell">
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
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500 text-sm">Nenhum registro de envio encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if($deliveries->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $deliveries->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin>