<div>

@section('page-title', 'Auditoría ISO 27001')

{{-- ══ Header ══════════════════════════════════════════════ --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="rounded p-4 text-white d-flex justify-content-between align-items-center"
             style="background:linear-gradient(135deg,#1A2E4A 0%,#2D5F8A 100%);">
            <div>
                <h4 class="font-weight-bold mb-1">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Auditoría de Seguridad
                </h4>
                <p class="mb-1" style="opacity:.9;font-size:.95rem;">
                    Registro de accesos conforme a ISO 27001
                </p>
                <p class="mb-0" style="opacity:.7;font-size:.82rem;">
                    <i class="far fa-calendar-alt mr-1"></i>
                    {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>
            <div class="d-none d-md-block">
                <img src="{{ asset(config('clinica.logo')) }}" alt="{{ config('clinica.nombre_largo') }}"
                     style="height:70px;opacity:.85"
                     onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</div>

{{-- ══ Filtros ══════════════════════════════════════════════ --}}
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i> Filtros
        </h3>
    </div>
    <div class="card-body">
        <div class="row g-2 align-items-end">

            {{-- Acción --}}
            <div class="col-12 col-md-3">
                <label class="small font-weight-bold text-muted mb-1">Tipo de acción</label>
                <select wire:model.live="filtro_accion" class="form-control form-control-sm">
                    <option value="">— Todas las acciones —</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="ver_historial">Ver historial</option>
                    <option value="ver_paciente">Ver paciente</option>
                    <option value="editar_paciente">Editar paciente</option>
                    <option value="generar_factura">Generar factura</option>
                    <option value="procesar_pago">Procesar pago</option>
                    <option value="ver_consulta">Ver consulta</option>
                    <option value="cambio_estado_paciente">Cambio estado</option>
                </select>
            </div>

            {{-- Usuario --}}
            <div class="col-12 col-md-3">
                <label class="small font-weight-bold text-muted mb-1">Buscar usuario</label>
                <input type="text"
                       wire:model.live.debounce.400ms="filtro_usuario"
                       placeholder="Nombre del usuario..."
                       class="form-control form-control-sm">
            </div>

            {{-- Desde --}}
            <div class="col-12 col-md-2">
                <label class="small font-weight-bold text-muted mb-1">Desde</label>
                <input type="date"
                       wire:model.live="fecha_desde"
                       class="form-control form-control-sm">
            </div>

            {{-- Hasta --}}
            <div class="col-12 col-md-2">
                <label class="small font-weight-bold text-muted mb-1">Hasta</label>
                <input type="date"
                       wire:model.live="fecha_hasta"
                       class="form-control form-control-sm">
            </div>

            {{-- Limpiar --}}
            <div class="col-12 col-md-2">
                <button wire:click="limpiarFiltros"
                        class="btn btn-secondary btn-sm btn-block">
                    <i class="fas fa-times mr-1"></i> Limpiar filtros
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ══ Tabla ════════════════════════════════════════════════ --}}
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-list-alt mr-1"></i>
            Registros de auditoría
        </h3>
        <span class="badge badge-primary">
            {{ $this->registros->total() }} registros
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="pl-3" style="width:180px">Usuario</th>
                        <th style="width:90px">Rol</th>
                        <th style="width:160px">Acción</th>
                        <th>Registro afectado</th>
                        <th style="width:120px">IP</th>
                        <th style="width:150px">Fecha y hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->registros as $reg)
                        <tr>
                            {{-- Usuario --}}
                            <td class="pl-3 align-middle">
                                @if($reg->user)
                                    <div class="font-weight-bold text-sm">{{ $reg->user->name }}</div>
                                    <small class="text-muted">{{ $reg->user->email }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Rol --}}
                            <td class="align-middle">
                                @if($reg->user)
                                    <span class="badge badge-secondary" style="font-size:.72rem">
                                        {{ $reg->user->role ?? '—' }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Acción con badge --}}
                            <td class="align-middle">
                                @php
                                    $badgeClass = match($reg->accion) {
                                        'login', 'logout'                         => 'badge-primary',
                                        'ver_historial', 'ver_consulta',
                                        'ver_paciente'                            => 'badge-secondary',
                                        'editar_paciente'                         => 'badge-warning',
                                        'generar_factura', 'procesar_pago'       => 'badge-success',
                                        'cambio_estado_paciente'                  => 'badge-info',
                                        default                                   => 'badge-light',
                                    };
                                    $iconos = [
                                        'login'                  => 'fa-sign-in-alt',
                                        'logout'                 => 'fa-sign-out-alt',
                                        'ver_historial'          => 'fa-file-medical-alt',
                                        'ver_paciente'           => 'fa-user',
                                        'ver_consulta'           => 'fa-stethoscope',
                                        'editar_paciente'        => 'fa-user-edit',
                                        'generar_factura'        => 'fa-file-invoice',
                                        'procesar_pago'          => 'fa-cash-register',
                                        'cambio_estado_paciente' => 'fa-exchange-alt',
                                    ];
                                @endphp
                                <span class="badge {{ $badgeClass }}" style="font-size:.78rem;padding:.35em .6em">
                                    <i class="fas {{ $iconos[$reg->accion] ?? 'fa-circle' }} mr-1"></i>
                                    {{ str_replace('_', ' ', $reg->accion) }}
                                </span>
                            </td>

                            {{-- Registro afectado --}}
                            <td class="align-middle">
                                @if($reg->modelo)
                                    <span class="text-muted" style="font-size:.82rem">
                                        {{ $reg->modelo }}
                                        @if($reg->modelo_id)
                                            #{{ $reg->modelo_id }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="align-middle">
                                <code style="font-size:.78rem">{{ $reg->ip }}</code>
                            </td>

                            {{-- Fecha --}}
                            <td class="align-middle text-muted" style="font-size:.82rem">
                                {{ $reg->created_at?->format('d/m/Y') }}<br>
                                <strong>{{ $reg->created_at?->format('H:i:s') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-shield-alt fa-2x mb-2 d-block opacity-25"></i>
                                Sin registros de auditoría para los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    @if($this->registros->hasPages())
        <div class="card-footer">
            {{ $this->registros->links() }}
        </div>
    @endif
</div>

</div>
