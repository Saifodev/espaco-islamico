{{-- resources/views/auth/accept-invitation.blade.php --}}
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <h2 class="text-xl font-bold mb-2">Aceitar Convite</h2>
        <p>Defina sua senha para ativar sua conta.</p>
    </div>

    <form method="POST" action="{{ route('invitation.accept.post') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Senha</label>
            <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres, com letras maiúsculas, minúsculas e números.</p>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Senha</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            type="password"
                            name="password_confirmation"
                            required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 font-bold py-2 px-4 rounded">
                Ativar Conta
            </button>
        </div>
    </form>
</x-guest-layout>