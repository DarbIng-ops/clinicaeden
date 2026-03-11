<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:16px;margin-bottom:20px;">
            <p style="font-weight:bold;margin-bottom:8px;color:#856404;">
                🧪 Entorno de demostración
            </p>
            <p style="font-size:13px;color:#856404;margin-bottom:10px;">
                Contraseña para todos los roles: <strong>password</strong>
            </p>
            <table style="width:100%;font-size:12px;border-collapse:collapse;">
                <tr style="background:#ffeeba;">
                    <th style="padding:4px 8px;text-align:left;border:1px solid #ffc107;">Rol</th>
                    <th style="padding:4px 8px;text-align:left;border:1px solid #ffc107;">Correo</th>
                </tr>
                <tr>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Admin</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">admin@clinicaeden.com</td>
                </tr>
                <tr style="background:#fffdf0;">
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Recepcionista</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">recepcion@clinicaeden.com</td>
                </tr>
                <tr>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Médico General</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">medico.general@clinicaeden.com</td>
                </tr>
                <tr style="background:#fffdf0;">
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Médico Especialista</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">medico.especialista@clinicaeden.com</td>
                </tr>
                <tr>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Jefe Enfermería</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">jefe.enfermeria@clinicaeden.com</td>
                </tr>
                <tr style="background:#fffdf0;">
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Auxiliar Enfermería</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">auxiliar.enfermeria@clinicaeden.com</td>
                </tr>
                <tr>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">Cajero</td>
                    <td style="padding:4px 8px;border:1px solid #ffc107;">caja@clinicaeden.com</td>
                </tr>
            </table>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
