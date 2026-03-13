{{-- resources/views/admin/newsletters/form.blade.php --}}
<x-admin>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-serif font-semibold text-gray-800">
                    {{ $newsletter->exists ? 'Editar Newsletter' : 'Nova Newsletter' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $newsletter->exists ? 'Atualize as informações da newsletter' : 'Crie uma nova newsletter para seus assinantes' }}
                </p>
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
                <form
                    action="{{ $newsletter->exists ? route('admin.newsletters.update', $newsletter) : route('admin.newsletters.store') }}"
                    method="POST">
                    @csrf
                    @if ($newsletter->exists)
                        @method('PUT')
                    @endif

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

                        <!-- Subject -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Assunto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="subject" id="subject"
                                value="{{ old('subject', $newsletter->subject) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors"
                                placeholder="Ex: Novidades da semana">
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Conteúdo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="content" rows="15"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors tinymce"
                                placeholder="Escreva o conteúdo da sua newsletter...">{{ old('content', $newsletter->content) }}</textarea>
                        </div>

                        <!-- Schedule -->
                        <div class="mb-6">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Agendar para (opcional)
                            </label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                                value="{{ old('scheduled_at', $newsletter->scheduled_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full md:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-colors">
                            <p class="text-xs text-gray-500 mt-1">
                                Deixe em branco para salvar como rascunho.
                            </p>
                        </div>

                        <!-- Info Block -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        <strong class="font-medium">{{ $subscribersCount }}</strong> assinantes ativos
                                        receberão esta newsletter.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Campos marcados com <span class="text-red-500">*</span> são obrigatórios
                        </p>
                        <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
                            <a href="{{ redirect()->back()->getTargetUrl() }}"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                Cancelar
                            </a>

                            @if (!$newsletter->exists || $newsletter->isDraft())
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center">
                                    <i class="fas fa-save mr-2"></i>
                                    Salvar Rascunho
                                </button>

                                @if ($subscribersCount > 0)
                                    <button type="submit" name="schedule" value="1"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        Agendar Envio
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- <script src="https://cdn.tiny.cloud/1/{{ env('TINY_MCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> --}}
        <script>
            tinymce.init({
                selector: '.tinymce',
                license_key: 'gpl',
                height: 500,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic backcolor | image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | fullscreen',
                // tinydrive_token_provider: (success, failure) => {
                //     success({
                //         token: "{{ env('TINYDRIVE_TOKEN_PROVIDER_KEY') }}"
                //     });
                // },
                content_style: 'body { font-family:Figtree,Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; color:#333; }',
                mobile: {
                    menubar: true,
                    toolbar: 'undo redo | bold italic | bullist numlist'
                }
            });
        </script>
    @endpush
</x-admin>
