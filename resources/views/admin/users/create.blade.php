{{-- resources/views/admin/users/create.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">Novo Usuário</h2>
                <p class="text-sm text-gray-600 mt-1">Adicione um novo usuário ao sistema</p>
            </div>
            <div class="mt-4 md:mt-0">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="p-8">
                        @if ($errors->any())
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                                    <span class="font-medium">Por favor, corrija os seguintes erros:</span>
                                </div>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Coluna Esquerda -->
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nome completo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                        placeholder="Digite o nome completo">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                        placeholder="exemplo@dominio.com">
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status
                                    </label>
                                    <select name="status" id="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Ativo
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Inativo</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Usuários inativos não podem fazer login</p>
                                </div>
                            </div>

                            <!-- Coluna Direita -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Perfis de acesso
                                    </label>
                                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        @foreach ($roles as $role)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                    id="role_{{ $role->id }}"
                                                    {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                                                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                                <label for="role_{{ $role->id }}" class="ml-3">
                                                    <span
                                                        class="text-sm font-medium text-gray-700">{{ ucfirst($role->name) }}</span>
                                                    @if ($role->name === 'admin')
                                                        <span class="text-xs text-red-500 ml-2">(Acesso total)</span>
                                                    @elseif($role->name === 'editor')
                                                        <span class="text-xs text-blue-500 ml-2">(Gerencia
                                                            conteúdo)</span>
                                                    @elseif($role->name === 'author')
                                                        <span class="text-xs text-green-500 ml-2">(Cria artigos)</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center space-x-3">
                                        <input type="checkbox" name="send_invite" value="1"
                                            {{ old('send_invite') ? 'checked' : '' }}
                                            class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-200">
                                        <span class="text-sm font-medium text-gray-700">Enviar convite por email</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2 ml-7">
                                        O usuário receberá um email com instruções para acessar o sistema e criar sua
                                        senha.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Campos marcados com <span class="text-red-500">*</span> são obrigatórios
                        </p>
                        <div class="flex items-center space-x-3">
                            <a href="{{ redirect()->back()->getTargetUrl() }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Salvar Usuário
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin>
