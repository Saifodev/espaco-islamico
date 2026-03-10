{{-- resources/views/admin/users/show.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">Detalhes do Usuário</h2>
                <p class="text-sm text-gray-600 mt-1">Informações completas da conta</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Editar
                </a>
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
            
            <!-- Profile Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6">
                    <div class="flex items-center">
                        <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center">
                            <span class="text-4xl font-bold text-green-600">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-2xl font-serif font-bold text-white">{{ $user->name }}</h3>
                            <p class="text-green-100 flex items-center mt-1">
                                <i class="fas fa-envelope mr-2"></i>
                                {{ $user->email }}
                            </p>
                        </div>
                        <div class="ml-auto">
                            @if($user->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-300 text-green-900">
                                    <i class="fas fa-circle text-xs mr-2"></i>
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-300 text-red-900">
                                    <i class="fas fa-circle text-xs mr-2"></i>
                                    Inativo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 divide-x divide-gray-200">
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->articles_count ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Artigos publicados</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->comments_count ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Comentários</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->roles->count() }}</p>
                        <p class="text-xs text-gray-500">Perfis de acesso</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $user->last_login_at ? 'Online' : 'Offline' }}</p>
                        <p class="text-xs text-gray-500">Status atual</p>
                    </div>
                </div>
            </div>

            <!-- Detalhes Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Informações da Conta -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Perfis e Permissões -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                            Perfis e Permissões
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium
                                    @if($role->name === 'admin') bg-red-100 text-red-800
                                    @elseif($role->name === 'editor') bg-blue-100 text-blue-800
                                    @elseif($role->name === 'author') bg-green-100 text-green-800
                                    @elseif($role->name === 'developer') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    <i class="fas 
                                        @if($role->name === 'admin') fa-crown mr-2
                                        @elseif($role->name === 'editor') fa-edit mr-2
                                        @elseif($role->name === 'author') fa-pen mr-2
                                        @elseif($role->name === 'developer') fa-code mr-2
                                        @else fa-user mr-2 @endif">
                                    </i>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Atividade Recente -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-history text-green-600 mr-2"></i>
                            Atividade Recente
                        </h4>
                        
                        @php
                            $atividades = collect([
                                $user->created_at ? [
                                    'tipo' => 'created',
                                    'descricao' => 'Conta criada',
                                    'data' => $user->created_at,
                                    'icone' => 'fa-user-plus',
                                    'cor' => 'blue'
                                ] : null,
                                $user->invitation_sent_at ? [
                                    'tipo' => 'invitation_sent',
                                    'descricao' => 'Convite enviado',
                                    'data' => $user->invitation_sent_at,
                                    'icone' => 'fa-envelope',
                                    'cor' => 'yellow'
                                ] : null,
                                $user->invitation_accepted_at ? [
                                    'tipo' => 'invitation_accepted',
                                    'descricao' => 'Convite aceito',
                                    'data' => $user->invitation_accepted_at,
                                    'icone' => 'fa-check-circle',
                                    'cor' => 'green'
                                ] : null,
                                $user->last_login_at ? [
                                    'tipo' => 'login',
                                    'descricao' => 'Último login',
                                    'data' => $user->last_login_at,
                                    'icone' => 'fa-sign-in-alt',
                                    'cor' => 'purple'
                                ] : null,
                            ])->filter()->sortByDesc('data')->take(5);
                        @endphp

                        @if($atividades->isNotEmpty())
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($atividades as $index => $atividade)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-{{ $atividade['cor'] }}-100 flex items-center justify-center ring-8 ring-white">
                                                            <i class="fas {{ $atividade['icone'] }} text-{{ $atividade['cor'] }}-600 text-sm"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-900">{{ $atividade['descricao'] }}</p>
                                                        <p class="text-xs text-gray-500">{{ $atividade['data']->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 text-center py-4">Nenhuma atividade registrada</p>
                        @endif
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Informações de Segurança -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-lock text-green-600 mr-2"></i>
                            Segurança
                        </h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Último acesso</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca acessou' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Último IP</dt>
                                <dd class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $user->last_login_ip ?? 'Não registrado' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Email verificado</dt>
                                <dd class="text-sm font-medium mt-1">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600 flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Sim - {{ $user->email_verified_at->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-yellow-600 flex items-center">
                                            <i class="fas fa-clock mr-1"></i>
                                            Não verificado
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Convite -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-envelope-open-text text-green-600 mr-2"></i>
                            Status do Convite
                        </h4>
                        
                        @if($user->invitation_accepted_at)
                            <div class="text-center">
                                <div class="h-16 w-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-check-circle text-3xl text-green-600"></i>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">Convite aceito</p>
                                <p class="text-xs text-gray-500">{{ $user->invitation_accepted_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @elseif($user->invitation_sent_at)
                            <div class="text-center">
                                <div class="h-16 w-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-clock text-3xl text-yellow-600"></i>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">Convite pendente</p>
                                <p class="text-xs text-gray-500 mb-4">Enviado em {{ $user->invitation_sent_at->format('d/m/Y H:i') }}</p>
                                <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Reenviar Convite
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="text-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-envelope text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">Nenhum convite enviado</p>
                                <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Enviar Convite
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Ações de Senha -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h4 class="text-lg font-serif font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-key text-green-600 mr-2"></i>
                            Gerenciar Senha
                        </h4>
                        
                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Gerar uma nova senha temporária para este usuário?')"
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Resetar Senha
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            Uma nova senha temporária será gerada e enviada por email
                        </p>
                    </div>

                    <!-- Zona de Perigo (apenas para admin e não próprio usuário) -->
                    @can('delete users')
                        @if($user->id !== auth()->id())
                            <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                                <h4 class="text-lg font-serif font-semibold text-red-800 mb-4 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Zona de Perigo
                                </h4>
                                
                                <p class="text-sm text-red-600 mb-4">
                                    Excluir permanentemente este usuário e todos os dados associados.
                                </p>
                                
                                <form action="{{ route('admin.users.destroy', $user) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Tem certeza absoluta que deseja excluir este usuário? Esta ação é irreversível e todos os dados serão perdidos.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <i class="fas fa-trash mr-2"></i>
                                        Excluir Usuário
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-admin>