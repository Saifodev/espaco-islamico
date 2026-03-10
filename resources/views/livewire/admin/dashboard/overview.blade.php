{{-- resources/views/livewire/admin/dashboard/visao-geral.blade.php --}}
<div class="space-y-8">
    <!-- Métricas principais em cards elegantes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Artigos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Artigos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metricas['artigos_publicados']) }}
                    </p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>+{{ $metricas['artigos_hoje'] }}
                        </span>
                        <span class="text-gray-400 text-sm mx-2">•</span>
                        <span class="text-yellow-600 text-sm">{{ $metricas['rascunhos'] }} rascunhos</span>
                    </div>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-newspaper text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Visualizações -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Visualizações</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ number_format($metricas['visualizacoes_total']) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-blue-600 text-sm font-medium">
                            <i class="fas fa-eye mr-1"></i>{{ number_format($metricas['visualizacoes_periodo']) }} no
                            período
                        </span>
                    </div>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Comentários -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Comentários</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metricas['comentarios_total']) }}
                    </p>
                    <div class="flex items-center mt-2 space-x-2">
                        <span
                            class="text-green-600 text-xs px-2 py-1 bg-green-50 rounded-full">{{ $metricas['comentarios_aprovados'] }}
                            aprov.</span>
                        <span
                            class="text-yellow-600 text-xs px-2 py-1 bg-yellow-50 rounded-full">{{ $metricas['comentarios_pendentes'] }}
                            pend.</span>
                    </div>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-comments text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Assinantes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Assinantes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($metricas['assinantes_total']) }}
                    </p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-user-plus mr-1"></i>+{{ $metricas['novos_assinantes'] }} novos
                        </span>
                    </div>
                </div>
                <div class="h-12 w-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-envelope-open-text text-2xl text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos lado a lado -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de visualizações -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-serif font-semibold text-gray-800">Visualizações no tempo</h3>
                <span class="text-xs text-gray-500">Últimos {{ count($graficos['visualizacoes']) }} dias</span>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="graficoVisualizacoes"></canvas>
            </div>
        </div>

        <!-- Gráfico de tipos de conteúdo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-serif font-semibold text-gray-800">Distribuição por tipo</h3>
                <span class="text-xs text-gray-500">Total de publicações</span>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="graficoTipos"></canvas>
            </div>
        </div>
    </div>

    <!-- Ações rápidas e insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ações rápidas -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Ações rápidas</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.articles.create') }}"
                    class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-green-50 transition-colors group">
                    <div
                        class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200">
                        <i class="fas fa-pen-fancy text-green-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Novo artigo</p>
                        <p class="text-xs text-gray-500">Publicar novo conteúdo</p>
                    </div>
                </a>

                {{-- <a href="#"
                    class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-yellow-50 transition-colors group">
                    <div
                        class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-yellow-200">
                        <i class="fas fa-comment-dots text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Moderar comentários</p>
                        <p class="text-xs text-gray-500">{{ $metricas['comentarios_pendentes'] }} aguardando</p>
                    </div>
                </a>

                <a href="#"
                    class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-purple-50 transition-colors group">
                    <div
                        class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200">
                        <i class="fas fa-mail-bulk text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Nova newsletter</p>
                        <p class="text-xs text-gray-500">Enviar para {{ number_format($metricas['assinantes_total']) }}
                            assinantes</p>
                    </div>
                </a> --}}

                <a href="{{ route('admin.users.create') }}"
                    class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors group">
                    <div
                        class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Novo usuário</p>
                        <p class="text-xs text-gray-500">Convidar colaborador</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Insights rápidos -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-serif font-semibold text-gray-800 mb-4">Insights rápidos</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        <span class="text-xs font-semibold text-green-700 uppercase tracking-wider">Melhor
                            horário</span>
                    </div>
                    <p class="text-xl font-bold text-gray-800">{{ $atividadeComunidade['horario_pico'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Maior engajamento dos leitores</p>
                </div>

                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                        <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Tempo médio de
                            leitura</span>
                    </div>
                    <p class="text-xl font-bold text-gray-800">{{ $metricas['tempo_leitura_medio'] }} min</p>
                    <p class="text-xs text-gray-600 mt-1">Por artigo publicado</p>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-percent text-purple-600 mr-2"></i>
                        <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Taxa de
                            engajamento</span>
                    </div>
                    <p class="text-xl font-bold text-gray-800">{{ $atividadeComunidade['taxa_engajamento'] }}%</p>
                    <p class="text-xs text-gray-600 mt-1">Comentários por visualização</p>
                </div>

                <div class="p-4 bg-amber-50 rounded-lg border border-amber-100">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-tags text-amber-600 mr-2"></i>
                        <span class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Categorias
                            ativas</span>
                    </div>
                    <p class="text-xl font-bold text-gray-800">{{ $metricas['total_categorias'] }}</p>
                    <p class="text-xs text-gray-600 mt-1">Com conteúdo publicado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts dos gráficos -->
    @once
        @push('scripts')
            <script>
                function initCharts() {

                    if (window.graficoVis instanceof Chart) {
                        window.graficoVis.destroy();
                    }

                    if (window.graficoTipos instanceof Chart) {
                        window.graficoTipos.destroy();
                    }

                    const ctxVis = document.getElementById('graficoVisualizacoes')?.getContext('2d');
                    const ctxTipos = document.getElementById('graficoTipos')?.getContext('2d');

                    if (ctxVis) {
                        window.graficoVis = new Chart(ctxVis, {
                            type: 'line',
                            data: {
                                labels: @json(array_column($graficos['visualizacoes'], 'data')),
                                datasets: [{
                                    label: 'Visualizações',
                                    data: @json(array_column($graficos['visualizacoes'], 'valor')),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16,185,129,0.1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false
                            }
                        });
                    }

                    if (ctxTipos) {
                        window.graficoTipos = new Chart(ctxTipos, {
                            type: 'doughnut',
                            data: {
                                labels: @json($graficos['tipos_conteudo']->pluck('rotulo')),
                                datasets: [{
                                    data: @json($graficos['tipos_conteudo']->pluck('valor')),
                                    backgroundColor: @json($graficos['tipos_conteudo']->pluck('cor')),
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%'
                            }
                        });
                    }

                }

                document.addEventListener('livewire:load', initCharts)
                document.addEventListener('livewire:navigated', initCharts)
            </script>
        @endpush
    @endonce
</div>
