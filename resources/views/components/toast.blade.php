@php
    $type = null;
    $message = null;

    if (session('success')) {
        $type = 'success';
        $message = session('success');
    } elseif (session('error')) {
        $type = 'error';
        $message = session('error');
    } elseif (session('warning')) {
        $type = 'warning';
        $message = session('warning');
    } elseif (session('info')) {
        $type = 'info';
        $message = session('info');
    }

    $colors = [
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
        'warning' => 'bg-yellow-500',
        'info' => 'bg-blue-500',
    ];
@endphp

@if ($message)
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition class="fixed top-5 right-5 z-50">
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-white {{ $colors[$type] }}">

            <span class="text-sm font-medium">
                {{ $message }}
            </span>

            <button @click="show = false" class="text-white/80 hover:text-white">
                ✕
            </button>

        </div>
    </div>
@endif
