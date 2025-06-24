<x-guest-layout>
    <div class="card shadow">
        <div class="card-body p-4 p-sm-5">
            <!-- Logo -->
            <div class="text-center mb-4">
                <x-application-logo class="text-primary" style="width: 64px; height: 64px;" />
            </div>

            <h5 class="card-title text-center mb-4">{{ __('Iniciar Sesión') }}</h5>

            <!-- Session Status -->
            <x-auth-session-status class="mb-3" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <x-input-label for="email" :value="__('Correo electrónico')" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" 
                        required autofocus autocomplete="username" placeholder="nombre@ejemplo.com" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <x-input-label for="password" :value="__('Contraseña')" />
                        @if (Route::has('password.request'))
                            <a class="text-decoration-none small" href="{{ route('password.request') }}">
                                {{ __('¿Olvidó su contraseña?') }}
                            </a>
                        @endif
                    </div>
                    <x-text-input id="password" type="password" name="password" 
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <!-- Remember Me -->
                <div class="mb-3">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label class="form-check-label" for="remember_me">
                            {{ __('Mantener sesión activa') }}
                        </label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <x-primary-button class="btn-lg">
                        {{ __('Iniciar sesión') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
