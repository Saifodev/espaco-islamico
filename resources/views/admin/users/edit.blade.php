{{-- resources/views/admin/users/edit.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">Editar Usuário</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $user->name }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('admin.users.show', $user) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Ver detalhes
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
            
            <!-- Status do Convite -->
            @if($user->invitation_sent_at && !$user->invitation_accepted_at)
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-3 text-xl"></i>
                            <div>
                                <p class="font-medium text-yellow-800">Convite Pendente</p>
                                <p class="text-sm text-yellow-600">
                                    Enviado em {{ $user->invitation_sent_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-envelope mr-2"></i>
                                Reenviar Convite
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if($user->invitation_accepted_at)
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                        <div>
                            <p class="font-medium text-green-800">Convite Aceito</p>
                            <p class="text-sm text-green-600">
                                Aceito em {{ $user->invitation_accepted_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Formulário Principal -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-8">
                        @if($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                                    <span class="font-medium">Por favor, corrija os seguintes erros:</span>
                                </div>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Coluna Principal -->
                            <div class="lg:col-span-2 space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nome completo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Perfis de acesso
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        @foreach($roles as $role)
                                            <div class="flex items-center">
                                                <input type="checkbox" 
                                                       name="roles[]" 
                                                       value="{{ $role->name }}" 
                                                       id="role_{{ $role->id }}"
                                                       {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                                <label for="role_{{ $role->id }}" class="ml-3">
                                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($role->name) }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select name="status" 
                                            id="status"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Coluna Lateral - Informações da Conta -->
                            <div class="space-y-6">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-id-card text-gray-500 mr-2"></i>
                                        Informações da Conta
                                    </h4>
                                    <dl class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">ID:</dt>
                                            <dd class="font-medium text-gray-900">#{{ $user->id }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Criado por:</dt>
                                            <dd class="font-medium text-gray-900">{{ $user->creator->name ?? 'Sistema' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Criado em:</dt>
                                            <dd class="font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Último login:</dt>
                                            <dd class="font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y') : 'Nunca' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Ações de Senha -->
                                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                    <h4 class="text-sm font-medium text-yellow-800 mb-3 flex items-center">
                                        <i class="fas fa-key text-yellow-700 mr-2"></i>
                                        Gerenciar Senha
                                    </h4>
                                    <div class="space-y-2">
                                        @if(!$user->invitation_accepted_at)
                                            <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-envelope mr-2"></i>
                                                    Reenviar Convite
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Gerar uma nova senha temporária para este usuário?')"
                                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                                <i class="fas fa-sync-alt mr-2"></i>
                                                Resetar Senha
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-trash mr-2"></i>
                                    Excluir Usuário
                                </button>
                            </form>
                        @else
                            <div></div>
                        @endif
                        
                        <div class="flex items-center space-x-3">
                            <a href="{{ redirect()->back()->getTargetUrl() }}" 
                               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Atualizar Usuário
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin>