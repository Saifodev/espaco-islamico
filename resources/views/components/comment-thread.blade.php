{{-- resources/views/livewire/admin/partials/comment-thread.blade.php --}}
@props(['comment'])

<div class="bg-white shadow-sm rounded-lg p-6" wire:key="comment-{{ $comment->id }}">
    <div class="flex items-start space-x-3">
        {{-- Checkbox para seleção --}}
        {{-- <div class="flex-shrink-0 pt-1">
            <input type="checkbox"
                   wire:model="selectedComments"
                   value="{{ $comment->id }}"
                   class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
        </div> --}}

        {{-- Avatar --}}
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                <span class="text-sm font-medium text-gray-600">
                    {{ strtoupper(substr($comment->name, 0, 1)) }}
                </span>
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <h4 class="text-sm font-medium text-gray-900">{{ $comment->name }}</h4>
                    <span class="text-xs text-gray-500">&lt;{{ $comment->email }}&gt;</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        @if($comment->status === 'approved') bg-green-100 text-green-800
                        @elseif($comment->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($comment->status === 'spam') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($comment->status) }}
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500">{{ $comment->created_date }}</span>
                    @if($comment->ip_address)
                        <span class="text-xs text-gray-400">IP: {{ $comment->ip_address }}</span>
                    @endif
                </div>
            </div>

            {{-- Conteúdo do comentário --}}
            @if($editingCommentId === $comment->id)
                <div class="mt-3">
                    <textarea wire:model="editingContent"
                              rows="3"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    <div class="mt-2 flex items-center space-x-2">
                        <button type="button"
                                wire:click="updateComment"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-medium text-white bg-green-600 hover:bg-green-700">
                            Salvar
                        </button>
                        <button type="button"
                                wire:click="cancelEdit"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                    </div>
                </div>
            @else
                <p class="mt-1 text-sm text-gray-700">{{ $comment->content }}</p>
            @endif

            {{-- Ações --}}
            @if (false)
            <div class="mt-3 flex items-center space-x-4">
                @if($comment->status !== 'approved')
                    <button wire:click="approveComment({{ $comment->id }})"
                            class="text-xs text-green-600 hover:text-green-800 font-medium">
                        Aprovar
                    </button>
                @endif
                
                @if($comment->status !== 'spam')
                    <button wire:click="markAsSpam('{{ $comment->id }}')"
                            class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                        Marcar como Spam
                    </button>
                @endif

                {{-- <button wire:click="startEdit({{ $comment->id }})"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Editar
                </button>

                <button wire:click="startReply({{ $comment->id }})"
                        class="text-xs text-gray-600 hover:text-gray-800 font-medium">
                    Responder
                </button> --}}

                <button wire:click="moveToTrash({{ $comment->id }})"
                        onclick="return confirm('Tem certeza que deseja mover este comentário para a lixeira?')"
                        class="text-xs text-red-600 hover:text-red-800 font-medium">
                    Lixeira
                </button>
            </div>
            @endif

            {{-- Formulário de resposta --}}
            @if($replyingToId === $comment->id)
                <div class="mt-4 pl-6 border-l-2 border-gray-200">
                    <textarea wire:model="replyContent"
                              rows="2"
                              placeholder="Escreva sua resposta..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                    <div class="mt-2 flex items-center space-x-2">
                        <button type="button"
                                wire:click="submitReply"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-xs font-medium text-white bg-green-600 hover:bg-green-700">
                            Enviar Resposta
                        </button>
                        <button type="button"
                                wire:click="cancelReply"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </button>
                    </div>
                </div>
            @endif

            {{-- Respostas --}}
            @if($comment->replies->isNotEmpty())
                <div class="mt-4 pl-6 space-y-4">
                    @foreach($comment->replies as $reply)
                        @include('livewire.admin.partials.comment-thread', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>