<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
        x-data="{
            formatBRPhone(v){
                const d=(v||'').replace(/\D+/g,'').slice(0,11);
                if(d.length<=2) return `(${d}`;
                if(d.length<=6) return `(${d.slice(0,2)}) ${d.slice(2)}`;
                if(d.length<=10) return `(${d.slice(0,2)}) ${d.slice(2,6)}-${d.slice(6)}`;
                return `(${d.slice(0,2)}) ${d.slice(2,7)}-${d.slice(7,11)}`; // 11 dígitos
            },
            digitsOnly(v){ return (v||'').replace(/\D+/g,''); },
            init(){
                const el=this.$refs.phone; if(el && el.value){ el.value=this.formatBRPhone(el.value); }
            }
        }"
        @submit.prevent="$refs.phone.value = digitsOnly($refs.phone.value); $el.submit()"
    >
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('Telefone')" />
            <x-text-input id="phone" x-ref="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel-national" placeholder="(11) 99999-9999" @input="$event.target.value = formatBRPhone($event.target.value)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="timezone" :value="__('Fuso horário')" />
                <select id="timezone" name="timezone" class="mt-1 block w-full rounded-md dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @php
                        $timezones = [
                            'America/Sao_Paulo' => 'America/Sao_Paulo',
                            'America/Bahia' => 'America/Bahia',
                            'America/Manaus' => 'America/Manaus',
                            'America/Recife' => 'America/Recife',
                            'UTC' => 'UTC',
                        ];
                        $tzValue = old('timezone', $user->timezone ?? config('app.timezone'));
                    @endphp
                    @foreach($timezones as $tz => $label)
                        <option value="{{ $tz }}" @selected($tzValue === $tz)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
            </div>

            <div>
                <x-input-label for="language" :value="__('Idioma')" />
                @php($langValue = old('language', $user->language ?? config('app.locale')))
                <select id="language" name="language" class="mt-1 block w-full rounded-md dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="pt-BR" @selected($langValue === 'pt-BR')>Português (Brasil)</option>
                    <option value="en" @selected($langValue === 'en')>English</option>
                    <option value="es" @selected($langValue === 'es')>Español</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('language')" />
            </div>
        </div>

        <div class="block mt-4">
            <label for="receives_email_notification" class="inline-flex items-center">
                <input id="receives_email_notification" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="receives_email_notification" value="1" @checked(old('receives_email_notification', $user->receives_email_notification))>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Desejo receber notificações por email sobre novas adorações.') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
