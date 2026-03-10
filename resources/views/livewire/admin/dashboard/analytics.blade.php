{{-- resources/views/livewire/admin/dashboard/analytics.blade.php --}}
<div class="space-y-6">
    <!-- Cards de métricas analíticas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Visualizações</p>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($metricas['visualizacoes_total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Engajados</p>
                    <p class="text-xl font-bold text-gray-900">
                        {{ number_format($atividadeComunidade['total_leitores_engajados']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percent text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Conversão</p>
                    <p class="text-xl font-bold text-gray-900">{{ $atividadeComunidade['taxa_engajamento'] }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-amber-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Pico</p>
                    <p class="text-xl font-bold text-gray-900">{{ $atividadeComunidade['horario_pico'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de comentários -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-serif font-semibold text-gray-800">Atividade de comentários</h3>
            <span class="text-xs text-gray-500">Últimos 7 dias</span>
        </div>
        <div class="h-80 relative" wire:ignore>
            <canvas id="graficoComentarios"></canvas>
        </div>
    </div>

    <!-- Top conteúdo -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mais lidos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Mais lidos da semana</h3>
            <div class="space-y-4">
                @forelse($topContent['mais_lidos_semana'] as $index => $artigo)
                    <div class="flex items-center">
                        <span
                            class="w-7 h-7 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm font-semibold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $artigo['titulo'] }}</p>
                            <p class="text-xs text-gray-500">por {{ $artigo['autor'] }}</p>
                        </div>
                        <span
                            class="text-sm font-semibold text-gray-700">{{ number_format($artigo['visualizacoes']) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Nenhum dado disponível</p>
                @endforelse
            </div>
        </div>

        <!-- Mais comentados -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Mais comentados</h3>
            <div class="space-y-4">
                @forelse($topContent['mais_comentados'] as $index => $artigo)
                    <div class="flex items-center">
                        <span
                            class="w-7 h-7 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-sm font-semibold mr-3">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $artigo['titulo'] }}</p>
                            <p class="text-xs text-gray-500">por {{ $artigo['autor'] }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">{{ $artigo['comentarios'] }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Nenhum dado disponível</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Autores top -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Autores mais produtivos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Autor
                        </th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Artigos</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Visualizações</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Média
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($topContent['autores_top'] as $autor)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $autor['nome'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $autor['artigos'] }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ number_format($autor['visualizacoes']) }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                {{ $autor['artigos'] > 0 ? number_format($autor['visualizacoes'] / $autor['artigos']) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                function initComentarioChart() {

                    if (window.graficoComentarios instanceof Chart) {
                        window.graficoComentarios.destroy();
                    }

                    const ctx = document.getElementById('graficoComentarios')?.getContext('2d');

                    if (!ctx) return;

                    window.graficoComentarios = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json(collect($graficos['comentarios'])->pluck('data')),
                            datasets: [{
                                label: 'Comentários',
                                data: @json(collect($graficos['comentarios'])->pluck('quantidade')),
                                backgroundColor: '#8b5cf6',
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                }

                document.addEventListener('livewire:load', initComentarioChart)
                document.addEventListener('livewire:navigated', initComentarioChart)
            </script>
        @endpush
    @endonce
</div>
