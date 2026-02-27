@extends('layouts.app')

@section('content')
<div class="py-8" style="background:#F2F4F7;min-height:100vh;">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="rounded-xl shadow-xl mb-6 p-6 flex justify-between items-center"
         style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%);">
        <div>
            <h1 class="text-3xl font-bold text-white">Mi Perfil</h1>
            <p class="mt-1 text-sm" style="color:#4A90C4;">
                Gestiona tu información personal y configuración de seguridad
            </p>
        </div>
        <a href="{{ $backRoute }}"
           class="hidden md:inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
           style="background:rgba(255,255,255,.15);"
           onmouseover="this.style.background='rgba(255,255,255,.25)'"
           onmouseout="this.style.background='rgba(255,255,255,.15)'">
            <i class="fas fa-arrow-left mr-2"></i> Volver al panel
        </a>
    </div>

    {{-- ── Alertas ──────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="flex items-center gap-3 mb-5 px-4 py-3 rounded-lg text-white text-sm font-medium shadow"
         style="background:#27AE60;">
        <i class="fas fa-check-circle text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-3 mb-5 px-4 py-3 rounded-lg text-white text-sm font-medium shadow"
         style="background:#C0392B;">
        <i class="fas fa-exclamation-circle text-lg mt-0.5"></i>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Layout principal ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Columna izquierda: Foto y datos breves ──────────────── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Tarjeta foto --}}
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <div class="relative inline-block mb-4">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-32 h-32 rounded-full object-cover mx-auto"
                             style="border:4px solid #2D5F8A;">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto flex items-center justify-center text-white text-4xl font-bold"
                             style="background:#2D5F8A;border:4px solid #1A2E4A;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->apellido ?? '', 0, 1)) }}
                        </div>
                    @endif

                    {{-- Botón cambiar foto --}}
                    <button type="button"
                            onclick="document.getElementById('photo-input').click()"
                            class="absolute bottom-0 right-0 w-9 h-9 rounded-full text-white shadow-lg flex items-center justify-center transition"
                            style="background:#27AE60;"
                            title="Cambiar foto"
                            onmouseover="this.style.background='#219a52'"
                            onmouseout="this.style.background='#27AE60'">
                        <i class="fas fa-camera text-sm"></i>
                    </button>
                </div>

                {{-- Formulario foto oculto --}}
                <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" id="photo-form">
                    @csrf
                    <input type="file" id="photo-input" name="photo" accept="image/*"
                           class="hidden" onchange="document.getElementById('photo-form').submit()">
                </form>

                <h2 class="text-xl font-bold text-gray-900">
                    {{ auth()->user()->name }} {{ auth()->user()->apellido }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->email }}</p>
                <span class="inline-block mt-3 px-4 py-1 rounded-full text-sm font-semibold text-white"
                      style="background:#2D5F8A;">
                    {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                </span>

                {{-- Info rápida --}}
                <div class="mt-5 space-y-2 text-sm text-left border-t pt-4">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-id-card w-5 text-center" style="color:#2D5F8A;"></i>
                        <span>{{ auth()->user()->dni ?? 'DNI no registrado' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-phone w-5 text-center" style="color:#2D5F8A;"></i>
                        <span>{{ auth()->user()->telefono ?? 'Sin teléfono' }}</span>
                    </div>
                    @if(auth()->user()->tipo_sangre)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-tint w-5 text-center" style="color:#C0392B;"></i>
                        <span>Tipo {{ auth()->user()->tipo_sangre }}</span>
                    </div>
                    @endif
                    @if(auth()->user()->edad)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-birthday-cake w-5 text-center" style="color:#E67E22;"></i>
                        <span>{{ auth()->user()->edad }} años</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tarjeta contacto de emergencia --}}
            <div class="bg-white rounded-xl shadow p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-phone-alt" style="color:#C0392B;"></i>
                    Contacto de Emergencia
                </h3>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    {{-- Campos ocultos para los demás campos requeridos --}}
                    <input type="hidden" name="name"     value="{{ auth()->user()->name }}">
                    <input type="hidden" name="apellido" value="{{ auth()->user()->apellido }}">
                    <input type="hidden" name="dni"      value="{{ auth()->user()->dni }}">

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Nombre</label>
                            <input type="text" name="contacto_emergencia_nombre"
                                   value="{{ auth()->user()->contacto_emergencia_nombre }}"
                                   placeholder="Nombre completo"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2"
                                   style="focus:ring-color:#2D5F8A;">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Teléfono</label>
                            <input type="tel" name="contacto_emergencia_telefono"
                                   value="{{ auth()->user()->contacto_emergencia_telefono }}"
                                   placeholder="Número de contacto"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2">
                        </div>
                    </div>
                    <button type="submit"
                            class="mt-4 w-full py-2 rounded-lg text-sm font-semibold text-white transition"
                            style="background:#C0392B;"
                            onmouseover="this.style.background='#a93226'"
                            onmouseout="this.style.background='#C0392B'">
                        <i class="fas fa-save mr-1"></i> Guardar Contacto
                    </button>
                </form>
            </div>

        </div>

        {{-- ── Columna derecha: Formularios principales ─────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Información Personal --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center gap-2"
                    style="border-left:4px solid #2D5F8A;padding-left:.6rem;">
                    <i class="fas fa-user" style="color:#2D5F8A;"></i>
                    Información Personal
                </h3>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        {{-- Apellido --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="apellido"
                                   value="{{ old('apellido', auth()->user()->apellido) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        {{-- DNI --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                DNI <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="dni"
                                   value="{{ old('dni', auth()->user()->dni) }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        {{-- Fecha nacimiento --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                Fecha de Nacimiento
                            </label>
                            <input type="date" name="fecha_nacimiento"
                                   value="{{ old('fecha_nacimiento', optional(auth()->user()->fecha_nacimiento)->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        {{-- Sexo --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Sexo</label>
                            <select name="sexo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">Seleccionar</option>
                                <option value="M" {{ auth()->user()->sexo === 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ auth()->user()->sexo === 'F' ? 'selected' : '' }}>Femenino</option>
                                <option value="O" {{ auth()->user()->sexo === 'O' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        {{-- Teléfono --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Teléfono</label>
                            <input type="tel" name="telefono"
                                   value="{{ old('telefono', auth()->user()->telefono) }}"
                                   placeholder="Ej: 300 123 4567"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        {{-- Tipo de sangre --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Tipo de Sangre</label>
                            <select name="tipo_sangre"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="">Seleccionar</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $tipo)
                                    <option value="{{ $tipo }}" {{ auth()->user()->tipo_sangre === $tipo ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dirección --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Dirección</label>
                            <input type="text" name="direccion"
                                   value="{{ old('direccion', auth()->user()->direccion) }}"
                                   placeholder="Calle, ciudad, departamento"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background:#2D5F8A;"
                                onmouseover="this.style.background='#1A2E4A'"
                                onmouseout="this.style.background='#2D5F8A'">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Información Institucional (solo lectura) --}}
            @if(auth()->user()->especialidad || auth()->user()->numero_licencia)
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2"
                    style="border-left:4px solid #27AE60;padding-left:.6rem;">
                    <i class="fas fa-hospital-user" style="color:#27AE60;"></i>
                    Datos Institucionales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    @if(auth()->user()->especialidad)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Especialidad</p>
                        <p class="font-medium">{{ auth()->user()->especialidad }}</p>
                    </div>
                    @endif
                    @if(auth()->user()->numero_licencia)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Número de Licencia</p>
                        <p class="font-medium">{{ auth()->user()->numero_licencia }}</p>
                    </div>
                    @endif
                    @if(auth()->user()->tieneDiploma())
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Diploma</p>
                        <a href="{{ auth()->user()->diploma_url }}" target="_blank"
                           class="inline-flex items-center gap-1 text-blue-600 hover:underline text-sm font-medium">
                            <i class="fas fa-file-pdf"></i> Ver diploma
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Cambiar Contraseña --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-base font-bold text-gray-800 mb-5 flex items-center gap-2"
                    style="border-left:4px solid #E67E22;padding-left:.6rem;">
                    <i class="fas fa-lock" style="color:#E67E22;"></i>
                    Cambiar Contraseña
                </h3>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                Contraseña Actual <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="current_password" required
                                   autocomplete="current-password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            @error('current_password')
                                <p class="text-xs mt-1" style="color:#C0392B;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                    Nueva Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" required
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <p class="text-xs text-gray-400 mt-1">Mínimo 8 caracteres</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                    Confirmar Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required
                                       autocomplete="new-password"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                                class="px-6 py-2 rounded-lg text-sm font-semibold text-white transition"
                                style="background:#E67E22;"
                                onmouseover="this.style.background='#ca6a1b'"
                                onmouseout="this.style.background='#E67E22'">
                            <i class="fas fa-key mr-2"></i> Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>

        </div>{{-- fin col derecha --}}
    </div>{{-- fin grid --}}
</div>
</div>
@endsection
