@extends('layouts.app')

@section('content')
    <div class="bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <x-avatar-initials :name="$user->name" size="16" />
                <div>
                    <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                        {{ __('Profile') }}
                    </h2>
                    <div class="mt-1 flex items-center gap-2 text-sm">
                        <span class="text-gray-300">{{ $user->name }}</span>
                        @if ($user->email_verified_at)
                            <span class="inline-flex items-center rounded-md bg-green-600/20 px-2 py-0.5 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/30">{{ __('Email verificado') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-yellow-600/20 px-2 py-0.5 text-xs font-medium text-yellow-400 ring-1 ring-inset ring-yellow-600/30">{{ __('Email não verificado') }}</span>
                            <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                @csrf
                                <button type="submit" class="ml-2 inline-flex items-center rounded-md bg-indigo-600/20 px-2 py-0.5 text-xs font-medium text-indigo-300 ring-1 ring-inset ring-indigo-600/30 hover:bg-indigo-600/30">
                                    {{ __('Reenviar verificação') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
