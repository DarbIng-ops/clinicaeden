@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
@endpush

<x-guest-layout>
<style>
:root {
    --bg: #060C18;
    --bg-card: #0D1526;
    --cyan: #00D4FF;
    --teal: #0FB89D;
    --text: #E2E8F0;
    --muted: #64748B;
    --border: rgba(255,255,255,0.07);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { background: var(--bg) !important; }

.lp-wrap {
    min-height: 100vh;
    background: var(--bg);
    background-image:
        linear-gradient(rgba(0,212,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,212,255,0.025) 1px, transparent 1px);
    background-size: 44px 44px;
    font-family: 'DM Sans', sans-serif;
    color: var(--text);
    -webkit-font-smoothing: antialiased;
}

/* ── NAV ─────────────────────────────────── */
.lp-nav {
    position: sticky; top: 0; z-index: 100;
    background: rgba(6,12,24,0.88);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--border);
    padding: 14px 32px;
    display: flex; align-items: center; justify-content: space-between;
}
.nav-logo { display: flex; align-items: center; gap: 12px; }
.nav-dot {
    width: 10px; height: 10px;
    background: var(--cyan); border-radius: 50%; flex-shrink: 0;
    animation: pulse-dot 2.2s ease-in-out infinite;
    box-shadow: 0 0 8px rgba(0,212,255,0.5);
}
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 6px rgba(0,212,255,0.4); transform: scale(1); }
    50%      { box-shadow: 0 0 14px rgba(0,212,255,0.7); transform: scale(1.15); }
}
.nav-brand {
    font-family: 'Syne', sans-serif;
    font-size: 15px; font-weight: 800; color: #fff; line-height: 1;
}
.nav-brand .sep { color: rgba(255,255,255,0.22); margin: 0 6px; }
.nav-sub {
    font-size: 10px; font-weight: 700; color: var(--cyan);
    letter-spacing: 0.1em; opacity: 0.65;
    margin-top: 3px; text-transform: uppercase;
}
.nav-right { display: flex; align-items: center; gap: 16px; }
.badge-demo {
    display: flex; align-items: center; gap: 7px;
    background: rgba(0,212,255,0.07);
    border: 1px solid rgba(0,212,255,0.18);
    border-radius: 20px; padding: 5px 13px;
    font-size: 11px; font-weight: 600; color: var(--cyan);
    letter-spacing: 0.07em; text-transform: uppercase;
}
.dot-green {
    width: 7px; height: 7px; background: #22c55e; border-radius: 50%;
    animation: pulse-green 1.8s ease-in-out infinite;
}
@keyframes pulse-green {
    0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
    50%      { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
}
.nav-link { font-size: 12px; color: #475569; text-decoration: none; transition: color .18s; }
.nav-link:hover { color: var(--cyan); }

/* ── HERO ─────────────────────────────────── */
.lp-hero {
    text-align: center;
    padding: 72px 24px 44px;
    max-width: 740px; margin: 0 auto;
}
.hero-tag {
    display: inline-block;
    font-size: 11px; font-weight: 500; color: var(--teal);
    letter-spacing: 0.1em; text-transform: uppercase;
    margin-bottom: 22px; padding: 5px 16px;
    border: 1px solid rgba(15,184,157,0.2); border-radius: 20px;
    background: rgba(15,184,157,0.06);
}
.hero-title {
    font-family: 'Syne', sans-serif;
    font-size: clamp(30px,4.5vw,50px); font-weight: 800; color: #fff;
    line-height: 1.15; margin-bottom: 20px; letter-spacing: -0.02em;
}
.hero-title em {
    font-style: normal;
    background: linear-gradient(135deg, var(--cyan) 0%, var(--teal) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-desc { font-size: 15px; color: #7E8FA4; line-height: 1.75; max-width: 520px; margin: 0 auto; }

/* ── STATS ────────────────────────────────── */
.lp-stats {
    display: flex; flex-wrap: wrap; justify-content: center;
    max-width: 560px; margin: 0 auto 44px;
    background: var(--bg-card);
    border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
}
.stat-item {
    flex: 1; min-width: 110px;
    padding: 20px 16px; text-align: center;
    border-right: 1px solid var(--border);
}
.stat-item:last-child { border-right: none; }
.stat-value {
    font-family: 'Syne', sans-serif;
    font-size: 28px; font-weight: 800; color: var(--cyan);
    line-height: 1; margin-bottom: 5px;
}
.stat-label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; }

/* ── MÓDULOS ──────────────────────────────── */
.lp-modules {
    display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;
    max-width: 700px; margin: 0 auto 60px; padding: 0 24px;
}
.mod-chip {
    padding: 7px 15px; border-radius: 20px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    font-size: 12px; color: #7E8FA4; font-weight: 500;
    transition: all .18s; cursor: default;
}
.mod-chip:hover { border-color: rgba(0,212,255,0.25); color: var(--cyan); background: rgba(0,212,255,0.04); }

/* ── TARJETA PRINCIPAL ────────────────────── */
.lp-card-wrap { max-width: 980px; margin: 0 auto; padding: 0 24px 48px; }
.lp-card {
    background: var(--bg-card);
    border: 1px solid var(--border); border-radius: 20px; overflow: hidden;
    display: flex; flex-wrap: wrap;
    box-shadow: 0 24px 80px rgba(0,0,0,0.4);
}
.card-left {
    flex: 1; min-width: 300px; padding: 36px 32px;
    border-right: 1px solid var(--border);
    background: rgba(0,0,0,0.12);
}
.card-right { flex: 1; min-width: 300px; padding: 36px 32px; }
.section-label {
    font-size: 10px; font-weight: 600; color: var(--muted);
    letter-spacing: 0.14em; text-transform: uppercase; margin-bottom: 20px;
}

/* password row */
.pwd-row {
    display: flex; align-items: center; gap: 12px;
    background: rgba(0,0,0,0.28);
    border: 1px solid rgba(0,212,255,0.12);
    border-radius: 10px; padding: 11px 15px; margin-bottom: 22px;
}
.pwd-info { flex: 1; }
.pwd-sublabel { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 3px; }
.pwd-value { font-family: 'Courier New', monospace; font-size: 18px; color: var(--cyan); letter-spacing: 3px; font-weight: bold; }
.btn-copy {
    background: rgba(0,212,255,0.08); border: 1px solid rgba(0,212,255,0.18);
    color: var(--cyan); padding: 6px 13px; border-radius: 7px;
    font-size: 11px; font-weight: 600; cursor: pointer;
    transition: all .18s; white-space: nowrap; font-family: 'DM Sans', sans-serif;
}
.btn-copy:hover { background: rgba(0,212,255,0.16); }

/* tabla credenciales */
.cred-table { width: 100%; border-collapse: collapse; }
.cred-table th {
    padding: 6px 8px; text-align: left;
    font-size: 10px; font-weight: 600; color: var(--muted);
    letter-spacing: 0.1em; text-transform: uppercase;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.cred-table td {
    padding: 8px 8px; font-size: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    vertical-align: middle;
}
.cred-table tr:last-child td { border-bottom: none; }
.cred-table tbody tr:hover td { background: rgba(0,212,255,0.025); }
.td-role { color: #CBD5E1; font-weight: 500; }
.td-email { color: #7DD3FC; font-family: monospace; font-size: 11px; }
.btn-use {
    background: transparent; border: 1px solid rgba(0,212,255,0.18);
    color: var(--cyan); padding: 4px 11px; border-radius: 5px;
    font-size: 11px; cursor: pointer; transition: all .18s;
    white-space: nowrap; font-family: 'DM Sans', sans-serif; font-weight: 500;
}
.btn-use:hover { background: rgba(0,212,255,0.1); border-color: var(--cyan); }

/* formulario */
.form-title {
    font-family: 'Syne', sans-serif;
    font-size: 21px; font-weight: 800; color: #fff;
    margin-bottom: 5px; letter-spacing: -0.01em;
}
.form-sub { font-size: 13px; color: var(--muted); margin-bottom: 24px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 12px; font-weight: 500; color: #8A9BB0; margin-bottom: 7px; }
.form-input {
    width: 100%; padding: 11px 14px;
    background: rgba(255,255,255,0.035);
    border: 1px solid rgba(255,255,255,0.09);
    border-radius: 10px; font-size: 14px; color: #fff;
    font-family: 'DM Sans', sans-serif;
    outline: none; transition: border-color .18s, background .18s;
}
.form-input:focus { border-color: rgba(0,212,255,0.35); background: rgba(0,212,255,0.025); }
.form-input::placeholder { color: #3A4A5C; }
.input-wrap { position: relative; }
.pwd-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; color: #475569; cursor: pointer;
    padding: 2px; transition: color .18s; line-height: 1;
    display: flex; align-items: center;
}
.pwd-toggle:hover { color: var(--cyan); }
.form-footer-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.form-check { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--muted); cursor: pointer; }
.form-check input[type="checkbox"] { accent-color: var(--cyan); cursor: pointer; }
.forgot-link { font-size: 12px; color: #475569; text-decoration: none; transition: color .18s; }
.forgot-link:hover { color: var(--cyan); }
.btn-submit {
    width: 100%; padding: 13px;
    background: linear-gradient(135deg, var(--cyan) 0%, var(--teal) 100%);
    color: #060C18; border: none; border-radius: 10px;
    font-size: 15px; font-weight: 700; font-family: 'Syne', sans-serif;
    cursor: pointer; letter-spacing: 0.01em;
    transition: opacity .18s, transform .15s;
}
.btn-submit:hover:not(:disabled) { opacity: 0.88; transform: translateY(-1px); }
.btn-submit:active:not(:disabled) { transform: translateY(0); }
.btn-submit:disabled { opacity: 0.55; cursor: not-allowed; }
.alert-ok {
    background: rgba(34,197,94,0.07); border: 1px solid rgba(34,197,94,0.18);
    border-radius: 8px; padding: 10px 14px;
    font-size: 13px; color: #86efac; margin-bottom: 16px;
}
.alert-err {
    background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.18);
    border-radius: 8px; padding: 10px 14px;
    font-size: 13px; color: #fca5a5; margin-bottom: 16px; line-height: 1.6;
}
.alert-err p + p { margin-top: 4px; }

/* ── WARNING ──────────────────────────────── */
.lp-warn { max-width: 980px; margin: 0 auto; padding: 0 24px 28px; }
.warn-box {
    background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.22);
    border-radius: 12px; padding: 14px 20px;
    font-size: 12px; color: #FCD34D; line-height: 1.75;
}
.warn-box strong { color: #FDE68A; }

/* ── FOOTER ───────────────────────────────── */
.lp-footer {
    max-width: 980px; margin: 0 auto; padding: 20px 24px 52px;
    display: flex; flex-wrap: wrap; align-items: center;
    justify-content: space-between; gap: 12px;
    border-top: 1px solid rgba(255,255,255,0.04);
}
.footer-copy { font-size: 12px; color: #2D3748; }
.footer-copy a { color: #2D3748; text-decoration: none; transition: color .18s; }
.footer-copy a:hover { color: #475569; }
.footer-pills { display: flex; gap: 7px; flex-wrap: wrap; }
.iso-pill {
    font-size: 10px; font-weight: 600; color: #334155;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 4px; padding: 3px 8px; letter-spacing: 0.06em;
}

/* ── RESPONSIVE ───────────────────────────── */
@media (max-width: 620px) {
    .lp-nav { padding: 12px 16px; }
    .nav-sub { display: none; }
    .nav-brand { font-size: 13px; }
    .lp-hero { padding: 48px 16px 32px; }
    .lp-card-wrap, .lp-warn, .lp-footer { padding-left: 16px; padding-right: 16px; }
    .card-left { border-right: none; border-bottom: 1px solid var(--border); }
    .card-left, .card-right { padding: 28px 20px; }
    .lp-modules { padding: 0 16px; }
    .lp-stats { margin-left: 16px; margin-right: 16px; max-width: unset; }
}
</style>

<div class="lp-wrap">

    {{-- ── NAV ── --}}
    <nav class="lp-nav">
        <div class="nav-logo">
            <span class="nav-dot"></span>
            <div>
                <div class="nav-brand">DarbinTech<span class="sep">·</span>PulsoCore SIH</div>
                <div class="nav-sub">Sistema de Información Hospitalaria</div>
            </div>
        </div>
        <div class="nav-right">
            <div class="badge-demo">
                <span class="dot-green"></span>
                Demo en vivo
            </div>
            <a href="https://darbin.tech" target="_blank" rel="noopener noreferrer" class="nav-link">
                darbin.tech ↗
            </a>
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="lp-hero">
        <div class="hero-tag">Sistema de Información Hospitalaria · Uruguay</div>
        <h1 class="hero-title">El <em>núcleo</em> de tu clínica,<br>en una sola pantalla.</h1>
        <p class="hero-desc">
            Gestión clínica integral: admisiones, historia clínica, agendas médicas,
            enfermería, facturación y auditoría RBAC en un sistema unificado y de código abierto.
        </p>
    </section>

    {{-- ── STATS ── --}}
    <div class="lp-stats">
        <div class="stat-item">
            <div class="stat-value">171</div>
            <div class="stat-label">Tests</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">7</div>
            <div class="stat-label">Roles activos</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">v3</div>
            <div class="stat-label">Versión</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color:var(--teal)">$0</div>
            <div class="stat-label">Licencia</div>
        </div>
    </div>

    {{-- ── MÓDULOS ── --}}
    <div class="lp-modules">
        <span class="mod-chip">Recepción</span>
        <span class="mod-chip">Médico General</span>
        <span class="mod-chip">Especialistas</span>
        <span class="mod-chip">Enfermería</span>
        <span class="mod-chip">Caja y factura PDF</span>
        <span class="mod-chip">Administración</span>
        <span class="mod-chip">Auditoría RBAC</span>
        <span class="mod-chip">Historia clínica</span>
    </div>

    {{-- ── TARJETA PRINCIPAL ── --}}
    <div class="lp-card-wrap">
        <div class="lp-card">

            {{-- Columna izquierda: credenciales demo --}}
            <div class="card-left">
                <div class="section-label">Credenciales de demo</div>

                <div class="pwd-row">
                    <div class="pwd-info">
                        <div class="pwd-sublabel">Contraseña · todos los roles</div>
                        <div class="pwd-value">password</div>
                    </div>
                    <button class="btn-copy" type="button" onclick="cp(this)">Copiar</button>
                </div>

                <table class="cred-table">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th>Correo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="td-role">Admin</td>
                            <td class="td-email">admin@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('admin@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Recepción</td>
                            <td class="td-email">recepcion@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('recepcion@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Médico</td>
                            <td class="td-email">medico.general@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('medico.general@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Especialista</td>
                            <td class="td-email">medico.especialista@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('medico.especialista@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Jefe Enf.</td>
                            <td class="td-email">jefe.enfermeria@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('jefe.enfermeria@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Auxiliar</td>
                            <td class="td-email">auxiliar@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('auxiliar@pulsocore.com')">Usar →</button></td>
                        </tr>
                        <tr>
                            <td class="td-role">Cajero</td>
                            <td class="td-email">caja@pulsocore.com</td>
                            <td><button class="btn-use" type="button" onclick="fill('caja@pulsocore.com')">Usar →</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Columna derecha: formulario Laravel --}}
            <div class="card-right">
                <div class="section-label">Acceso al sistema</div>
                <div class="form-title">Iniciar sesión</div>
                <p class="form-sub">Accede con tus credenciales o usa un rol demo.</p>

                @if (session('status'))
                    <div class="alert-ok">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert-err">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="login-form" onsubmit="handleSubmit(this)">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input
                            class="form-input"
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="input-wrap">
                            <input
                                class="form-input"
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                style="padding-right:42px"
                            >
                            <button class="pwd-toggle" type="button" id="pwd-toggle-btn"
                                onclick="togglePwd()" title="Mostrar/ocultar contraseña">
                                <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                                <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:none">
                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-footer-row">
                        <label class="form-check">
                            <input type="checkbox" name="remember">
                            Recuérdame
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit" id="submit-btn">
                        <span id="btn-text">Acceder al sistema</span>
                        <span id="btn-loading" style="display:none">Verificando…</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- ── AVISO WARNING ── --}}
    <div class="lp-warn">
        <div class="warn-box">
            ⚠️ <strong>Entorno de evaluación únicamente.</strong>
            No ingrese datos reales de pacientes ni información personal sensible en esta demo.
            Darbin Tech no se responsabiliza por el uso indebido de datos registrados por terceros en este entorno.
            Este sistema está disponible exclusivamente para evaluación técnica del producto.
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <footer class="lp-footer">
        <div class="footer-copy">
            PulsoCore SIH · v3 ·
            <a href="https://darbin.tech" target="_blank" rel="noopener">Darbin Tech</a>
            © 2026
        </div>
        <div class="footer-pills">
            <span class="iso-pill">ISO 27001</span>
            <span class="iso-pill">ISO 7101</span>
            <span class="iso-pill">ISO 9001</span>
        </div>
    </footer>

</div>

<script>
function fill(email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    document.getElementById('email').dispatchEvent(new Event('input'));
}

function cp(btn) {
    navigator.clipboard.writeText('password').then(function () {
        var orig = btn.textContent;
        btn.textContent       = '✓ Copiado';
        btn.style.background  = 'rgba(34,197,94,0.12)';
        btn.style.borderColor = 'rgba(34,197,94,0.28)';
        btn.style.color       = '#86efac';
        setTimeout(function () {
            btn.textContent       = orig;
            btn.style.background  = '';
            btn.style.borderColor = '';
            btn.style.color       = '';
        }, 1800);
    }).catch(function () {
        var ta = document.createElement('textarea');
        ta.value = 'password';
        ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
        btn.textContent = '✓ Copiado';
        setTimeout(function () { btn.textContent = 'Copiar'; }, 1800);
    });
}

function togglePwd() {
    var inp    = document.getElementById('password');
    var eyeOn  = document.getElementById('icon-eye');
    var eyeOff = document.getElementById('icon-eye-slash');
    if (inp.type === 'password') {
        inp.type             = 'text';
        eyeOn.style.display  = 'none';
        eyeOff.style.display = '';
    } else {
        inp.type             = 'password';
        eyeOn.style.display  = '';
        eyeOff.style.display = 'none';
    }
}

function handleSubmit(form) {
    var btn               = document.getElementById('submit-btn');
    var txt               = document.getElementById('btn-text');
    var loading           = document.getElementById('btn-loading');
    btn.disabled          = true;
    txt.style.display     = 'none';
    loading.style.display = 'inline';
}
</script>
</x-guest-layout>
