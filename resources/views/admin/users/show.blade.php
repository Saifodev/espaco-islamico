{{-- resources/views/admin/users/show.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalhes do Usuário') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-start">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div
                                class="h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                <span class="text-4xl font-bold text-white">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="ml-6 flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name_capitalized }}</h3>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    @if ($user->is_active)
                                        <span
                                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-xs mr-2 mt-1"></i>Ativo
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-circle text-xs mr-2 mt-1"></i>Inativo
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="mt-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($user->roles as $role)
                                        <span
                                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                            @if ($role->name === 'admin') bg-red-100 text-red-800
                                            @elseif($role->name === 'editor') bg-blue-100 text-blue-800
                                            @elseif($role->name === 'author') bg-green-100 text-green-800
                                            @elseif($role->name === 'developer') bg-purple-100 text-purple-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            <i
                                                class="fas 
                                                @if ($role->name === 'admin') fa-crown mr-2
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Account Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-id-card text-blue-500 mr-2"></i>
                            Informações da Conta
                        </h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">ID do Usuário</dt>
                                <dd class="text-sm font-medium text-gray-900">#{{ $user->id }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Criado por</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @if ($user->creator)
                                        <a href="{{ route('admin.users.show', $user->creator) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            {{ $user->creator->name }}
                                        </a>
                                    @else
                                        Sistema
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Criado em</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $user->created_at_formatted }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Última atualização</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Invitation Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-envelope text-yellow-500 mr-2"></i>
                            Status do Convite
                        </h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Status</dt>
                                <dd class="text-sm font-medium">
                                    @if ($user->invitation_accepted_at)
                                        <span class="text-green-600 flex items-center">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Aceito em {{ $user->invitation_accepted_at->format('d/m/Y H:i') }}
                                        </span>
                                    @elseif($user->invitation_sent_at)
                                        <span class="text-yellow-600 flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
                                            Pendente - Enviado em {{ $user->invitation_sent_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-600 flex items-center">
                                            <i class="fas fa-times-circle mr-2"></i>
                                            Não enviado
                                        </span>
                                    @endif
                                </dd>
                            </div>

                            @if ($user->email_verified_at)
                                <div>
                                    <dt class="text-xs text-gray-500 uppercase">Email Verificado</dt>
                                    <dd class="text-sm font-medium text-green-600">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        {{ $user->email_verified_at->format('d/m/Y H:i') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        @if (!$user->invitation_accepted_at)
                            <div class="mt-4">
                                <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-sm">
                                        <i class="fas fa-envelope mr-2"></i>
                                        {{ $user->invitation_sent_at ? 'Reenviar Convite' : 'Enviar Convite' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Security -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-shield-alt text-green-500 mr-2"></i>
                            Segurança
                        </h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Último login</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca acessou' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 uppercase">Último IP</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $user->last_login_ip ?? 'Não registrado' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4">
                            <button type="button"
                                onclick="if(confirm('Gerar nova senha temporária para este usuário?')) document.getElementById('resetPasswordForm').submit();"
                                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm">
                                <i class="fas fa-key mr-2"></i>
                                Resetar Senha
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-history text-purple-500 mr-2"></i>
                        Atividade Recente
                    </h4>

                    @php
                        $activities = [
                            [
                                'type' => 'created',
                                'description' => 'Usuário criado',
                                'date' => $user->created_at,
                                'icon' => 'fa-user-plus',
                                'color' => 'blue',
                            ],
                            [
                                'type' => 'invitation_sent',
                                'description' => 'Convite enviado',
                                'date' => $user->invitation_sent_at,
                                'icon' => 'fa-envelope',
                                'color' => 'yellow',
                            ],
                            [
                                'type' => 'invitation_accepted',
                                'description' => 'Convite aceito',
                                'date' => $user->invitation_accepted_at,
                                'icon' => 'fa-check-circle',
                                'color' => 'green',
                            ],
                            [
                                'type' => 'email_verified',
                                'description' => 'Email verificado',
                                'date' => $user->email_verified_at,
                                'icon' => 'fa-envelope-open-text',
                                'color' => 'teal',
                            ],
                        ];
                        $activities = array_filter($activities, fn($a) => !is_null($a['date']));
                        usort($activities, fn($a, $b) => $b['date']->timestamp - $a['date']->timestamp);
                    @endphp

                    @if (count($activities) > 0)
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @foreach ($activities as $index => $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span
                                                        class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-500 flex items-center justify-center ring-8 ring-white">
                                                        <i class="fas {{ $activity['icon'] }} text-white text-sm"></i>
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-900">{{ $activity['description'] }}
                                                        </p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                                        <time datetime="{{ $activity['date']->toIso8601String() }}">
                                                            {{ $activity['date']->diffForHumans() }}
                                                        </time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Nenhuma atividade registrada.</p>
                    @endif
                </div>
            </div>

            <!-- Danger Zone -->
            @can('delete users')
                @if ($user->id !== auth()->id())
                    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Zona de Perigo
                            </h4>

                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Excluir conta do usuário</p>
                                    <p class="text-sm text-gray-500">Esta ação é irreversível. Todos os dados associados
                                        serão removidos.</p>
                                </div>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-trash mr-2"></i>Excluir Usuário
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan
        </div>
    </div>

    <!-- Formulário Auxiliar para Reset de Senha -->
    <form id="resetPasswordForm" action="{{ route('admin.users.reset-password', $user) }}" method="POST"
        class="hidden">
        @csrf
    </form>
</x-admin>
