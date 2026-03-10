{{-- resources/views/livewire/admin/dashboard/content.blade.php --}}
<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Artigos Mais Lidos da Semana -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Mais Lidos da Semana</h3>
            <div class="space-y-4">
                @foreach ($topContent['mais_lidos_semana'] as $index => $article)
                    <div class="flex items-center space-x-4">
                        <span
                            class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-semibold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $article['titulo'] }}</p>
                            <p class="text-xs text-gray-500">por {{ $article['autor'] }} •
                                {{ $article['published_at'] }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ number_format($article['visualizacoes']) }} views
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Artigos Mais Lidos do Mês -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Mais Lidos do Mês</h3>
            <div class="space-y-4">
                @foreach ($topContent['mais_lidos_mes'] as $index => $article)
                    <div class="flex items-center space-x-4">
                        <span
                            class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-semibold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $article['titulo'] }}</p>
                            <p class="text-xs text-gray-500">por {{ $article['autor'] }} •
                                {{ $article['published_at'] }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ number_format($article['visualizacoes']) }} views
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Gráfico de Artigos por Categoria -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Artigos por Categoria</h3>
        <div class="h-80" wire:ignore>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <!-- Ranking de Autores -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Autores Mais Ativos</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Artigos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            de Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Média
                            Views/Artigo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($topContent['autores_top'] as $author)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $author['nome'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $author['artigos'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($author['visualizacoes']) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $author['artigos'] > 0 ? number_format($author['visualizacoes'] / $author['artigos']) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {

                const el = document.getElementById('categoryChart');

                if (!el) return;

                if (window.categoryChartInstance) {
                    window.categoryChartInstance.destroy();
                }

                const ctx = el.getContext('2d');

                window.categoryChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($graficos['categorias']->pluck('nome')),
                        datasets: [{
                            label: 'Artigos',
                            data: @json($graficos['categorias']->pluck('quantidade')),
                            backgroundColor: @json($graficos['categorias']->pluck('cor')),
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

            });
        </script>
    @endpush
</div>
