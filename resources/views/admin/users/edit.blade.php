{{-- resources/views/admin/users/edit.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Usuário') }}: {{ $user->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.show', $user) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-2"></i>Ver Usuário
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-6">
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Status do Convite -->
                        @if ($user->invitation_sent_at && !$user->invitation_accepted_at)
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                                    <div>
                                        <p class="font-bold">Convite Pendente</p>
                                        <p class="text-sm">
                                            Convite enviado em {{ $user->invitation_sent_at->format('d/m/Y H:i') }}.
                                            O usuário ainda não aceitou o convite.
                                        </p>
                                    </div>
                                    <div class="ml-auto">
                                        <form action="{{ route('admin.users.resend-invite', $user) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-2 px-4 rounded">
                                                <i class="fas fa-envelope mr-2"></i>Reenviar Convite
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($user->invitation_accepted_at)
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-3 text-green-600"></i>
                                    <div>
                                        <p class="font-bold">Convite Aceito</p>
                                        <p class="text-sm">
                                            Usuário ativou a conta em
                                            {{ $user->invitation_accepted_at->format('d/m/Y H:i') }}.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', $user->email) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            </div>

                            <!-- Perfis -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Perfis de Acesso</label>
                                <div class="space-y-2 max-h-60 overflow-y-auto p-3 border border-gray-200 rounded-md">
                                    @foreach ($roles as $role)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                id="role_{{ $role->id }}"
                                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                            <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">
                                                {{ ucfirst($role->name) }}
                                                @if ($role->name === 'admin')
                                                    <span class="text-xs text-red-500 ml-1">(Acesso total)</span>
                                                @elseif($role->name === 'editor')
                                                    <span class="text-xs text-blue-500 ml-1">(Gerencia conteúdo)</span>
                                                @elseif($role->name === 'author')
                                                    <span class="text-xs text-green-500 ml-1">(Cria artigos)</span>
                                                @elseif($role->name === 'developer')
                                                    <span class="text-xs text-purple-500 ml-1">(Acesso técnico)</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Selecione um ou mais perfis para o usuário.
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                    <option value="active"
                                        {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inactive"
                                        {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inativo
                                    </option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Usuários inativos não podem fazer login no sistema.
                                </p>
                            </div>

                            <!-- Informações Adicionais -->
                            <div class="col-span-2 mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Conta</h3>
                                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <span class="text-xs text-gray-500 block">Criado por</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $user->creator->name ?? 'Sistema' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Criado em</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $user->created_at_formatted }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-500 block">Última atualização</span>
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $user->updated_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Ações de Senha -->
                            <div class="col-span-2 mt-4">
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">Gerenciar Senha</h4>
                                            <p class="text-xs text-gray-500">Ações relacionadas à senha do usuário</p>
                                        </div>
                                        <div class="space-x-2">
                                            @if (!$user->invitation_accepted_at)
                                                <button type="button"
                                                    onclick="document.getElementById('resendInviteForm').submit();"
                                                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-2 px-4 rounded">
                                                    <i class="fas fa-envelope mr-2"></i>Reenviar Convite
                                                </button>
                                            @endif
                                            <button type="button"
                                                onclick="document.getElementById('resetPasswordForm').submit();"
                                                class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-2 px-4 rounded">
                                                <i class="fas fa-key mr-2"></i>Resetar Senha
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 text-right flex justify-between items-center">
                        <div>
                            @if ($user->id !== auth()->id())
                                <button type="button"
                                    onclick="if(confirm('Tem certeza que deseja excluir este usuário?')) document.getElementById('deleteUserForm').submit();"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-trash mr-2"></i>Excluir Usuário
                                </button>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('admin.users.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Atualizar Usuário
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Formulários Auxiliares -->
                <form id="deleteUserForm" action="{{ route('admin.users.destroy', $user) }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

                <form id="resendInviteForm" action="{{ route('admin.users.resend-invite', $user) }}" method="POST"
                    class="hidden">
                    @csrf
                </form>

                <form id="resetPasswordForm" action="{{ route('admin.users.reset-password', $user) }}"
                    method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</x-admin>
