<x-guest-layout>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f0f4f8;padding:20px;">
    <div style="display:flex;gap:24px;width:100%;max-width:960px;flex-wrap:wrap;align-items:stretch;">

        {{-- COLUMNA IZQUIERDA: Panel demo --}}
        <div style="flex:1;min-width:280px;background:#1e293b;border-radius:16px;padding:32px;color:#e2e8f0;">

            <div style="margin-bottom:24px;">
                <span style="background:#0ea5e9;color:white;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;letter-spacing:1px;">DEMO EN VIVO</span>
                <h2 style="margin-top:12px;font-size:22px;font-weight:700;color:white;line-height:1.3;">
                    Explora ClinicaEden<br>sin registrarte
                </h2>
                <p style="font-size:13px;color:#94a3b8;margin-top:8px;">
                    Selecciona cualquier rol y usa las credenciales para explorar el sistema completo.
                </p>
            </div>

            <div style="margin-bottom:20px;">
                <p style="font-size:12px;color:#94a3b8;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">Contraseña para todos</p>
                <div style="background:#0f172a;border-radius:8px;padding:10px 14px;font-family:monospace;font-size:15px;color:#38bdf8;letter-spacing:2px;">
                    password
                </div>
            </div>

            <table style="width:100%;font-size:12px;border-collapse:collapse;margin-bottom:24px;">
                <tr style="border-bottom:1px solid #334155;">
                    <th style="padding:6px 4px;text-align:left;color:#64748b;font-weight:600;">ROL</th>
                    <th style="padding:6px 4px;text-align:left;color:#64748b;font-weight:600;">CORREO</th>
                </tr>
                <tr style="border-bottom:1px solid #1e293b;">
                    <td style="padding:7px 4px;color:#e2e8f0;">Admin</td>
                    <td style="padding:7px 4px;color:#7dd3fc;font-size:11px;">admin@clinicaeden.com</td>
                </tr>
                <tr style="border-bottom:1px solid #1e293b;background:#0f172a20;">
                    <td style="padding:7px 4px;color:#e2e8f0;">Recepcionista</td>
                    <td style="padding:7px 4px;color:#7dd3fc;font-size:11px;">recepcion@clinicaeden.com</td>
                </tr>
                <tr style="border-bottom:1px solid #1e293b;">
                    <td style="padding:7px 4px;color:#e2e8f0;">Médico General</td>
                    <td style="padding:7px 4px;color:#7dd3fc;font-size:11px;">medico.general@clinicaeden.com</td>
                </tr>
                <tr>
                    <td style="padding:7px 4px;color:#e2e8f0;">Cajero</td>
                    <td style="padding:7px 4px;color:#7dd3fc;font-size:11px;">caja@clinicaeden.com</td>
                </tr>
            </table>

            <div style="background:#7f1d1d30;border:1px solid #ef444440;border-radius:8px;padding:12px;font-size:11px;color:#fca5a5;line-height:1.6;">
                ⚠️ <strong>Aviso importante:</strong><br>
                No ingrese datos reales de pacientes ni información personal en este entorno.
                Darbin Tech no se responsabiliza por el uso indebido de datos registrados
                por terceros en esta demo. Este entorno es exclusivamente para evaluación del sistema.
            </div>
        </div>

        {{-- COLUMNA DERECHA: Formulario login --}}
        <div style="flex:1;min-width:280px;background:white;border-radius:16px;padding:32px;display:flex;flex-direction:column;justify-content:center;">

            <div style="text-align:center;margin-bottom:28px;">
                <x-authentication-card-logo />
            </div>

            @if (session('status'))
                <div style="background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:10px;margin-bottom:16px;font-size:13px;color:#166534;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:10px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                        Correo electrónico
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                        placeholder="correo@ejemplo.com">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
                        Contraseña
                    </label>
                    <input type="password" name="password" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;"
                        placeholder="••••••••">
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:#6b7280;cursor:pointer;">
                        <input type="checkbox" name="remember">
                        Recuérdame
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            style="font-size:12px;color:#0ea5e9;text-decoration:none;">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit"
                    style="width:100%;padding:12px;background:#0ea5e9;color:white;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;">
                    Acceder
                </button>
            </form>
        </div>

    </div>
</div>
</x-guest-layout>
