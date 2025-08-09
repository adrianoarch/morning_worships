@php
    $status = session('status');
    $success = session('success');
    $error = session('error');
    $warning = session('warning');

    $hasAny = $status || $success || $error || $warning;

    $statusMap = [
        'profile-updated' => ['type' => 'success', 'text' => __('Perfil atualizado com sucesso.')],
        'password-updated' => ['type' => 'success', 'text' => __('Senha atualizada com sucesso.')],
        'verification-link-sent' => ['type' => 'info', 'text' => __('Um novo link de verificação foi enviado para seu e-mail.')],
        'user-deleted' => ['type' => 'success', 'text' => __('Conta excluída com sucesso.')],
    ];

    $resolved = null;
    if ($status) {
        $resolved = $statusMap[$status] ?? ['type' => 'info', 'text' => __($status)];
    }
@endphp

@if ($hasAny)
    <div aria-live="assertive" class="fixed inset-x-0 top-12 p-4 sm:p-6 pointer-events-none z-50">
        <div class="w-full flex flex-col items-start space-y-2">
            @if ($success)
                <x-toast type="success" :message="$success" />
            @endif

            @if ($warning)
                <x-toast type="warning" :message="$warning" />
            @endif

            @if ($error)
                <x-toast type="error" :message="$error" />
            @endif

            @if ($resolved)
                <x-toast :type="$resolved['type']" :message="$resolved['text']" />
            @endif
        </div>
    </div>
@endif
