{{-- resources/views/livewire/admin/dashboard/community.blade.php --}}
<div>
    <!-- Métricas da Comunidade -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total de Comentários</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $metricas['comentarios_total'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-comments text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex justify-between text-sm">
                <span class="text-green-600">{{ $metricas['comentarios_aprovados'] }} aprovados</span>
                <span class="text-yellow-600">{{ $metricas['comentarios_pendentes'] }} pendentes</span>
                <span class="text-red-600">{{ $metricas['comentarios_spam'] }} spam</span>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Leitores Engajados</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $atividadeComunidade['total_leitores_engajados'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-users text-2xl text-blue-600"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">
                Média de {{ $atividadeComunidade['media_comentarios_artigo'] }} comentários por artigo
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Taxa de Engajamento</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $atividadeComunidade['taxa_engajamento'] }}%</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-chart-pie text-2xl text-purple-600"></i>
                </div>
            </div>
            <p class="mt-4 text-sm text-gray-600">
                Horário mais ativo: {{ $atividadeComunidade['horario_pico'] }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Comentários Recentes -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Comentários Recentes</h3>
            <div class="space-y-4">
                @forelse($atividadeRecente['comentarios'] as $comment)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $comment['autor'] }}</p>
                            <p class="text-xs text-gray-500">{{ $comment['quando'] }} em "{{ $comment['artigo'] }}"</p>
                            <p class="text-sm text-gray-700 mt-2">{{ Str::limit($comment['conteudo'], 100) }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @if($comment['status'] === 'approved') bg-green-100 text-green-800
                            @elseif($comment['status'] === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($comment['status'] === 'spam') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($comment['status']) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Nenhum comentário recente.</p>
                @endforelse
            </div>
        </div>

        <!-- Pendentes de Moderação -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-serif font-semibold text-gray-800">Moderação Pendente</h3>
                @if($metricas['comentarios_pendentes'] > 0)
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                    {{ $metricas['comentarios_pendentes'] }} aguardando
                </span>
                @endif
            </div>
            <div class="space-y-4">
                @forelse($atividadeRecente['publicacoes'] as $comment)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $comment['autor'] }}</p>
                            <p class="text-xs text-gray-500">em "{{ $comment['titulo'] }}"</p>
                            <p class="text-sm text-gray-700 mt-2">{{ Str::limit($comment['conteudo'], 80) }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="approveComment({{ $comment['id'] }})" 
                                    class="text-green-600 hover:text-green-700 p-1"
                                    title="Aprovar">
                                <i class="fas fa-check"></i>
                            </button>
                            <button wire:click="markCommentAsSpam({{ $comment['id'] }})"
                                    class="text-red-600 hover:text-red-700 p-1"
                                    title="Marcar como Spam">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Nenhum comentário pendente.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Artigos Mais Comentados -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Artigos Mais Comentados</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artigo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentários</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topContent['mais_comentados'] as $article)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $article['titulo'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $article['autor'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $article['comentarios'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>