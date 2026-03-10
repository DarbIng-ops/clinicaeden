@extends('layouts.adminlte')

@section('title', 'Inicio — Recepción')
@section('page-title', 'Inicio')

@push('styles')
<style>
    :root {
        --eden-oscuro:  #1A2E4A;
        --eden-medio:   #2D5F8A;
        --eden-claro:   #4A90C4;
        --eden-verde:   #27AE60;
        --eden-naranja: #E67E22;
        --eden-rojo:    #C0392B;
        --eden-gris:    #F2F4F7;
    }
</style>
@endpush

@section('content')
<div>

    {{-- Banner bienvenida --}}
    <div class="card mb-4" style="background: linear-gradient(135deg, var(--eden-oscuro), var(--eden-medio)); border:none; border-radius:12px;">
        <div class="card-body py-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="text-white mb-1 font-weight-bold">¡Bienvenido, {{ auth()->user()->name }}!</h4>
                <p class="text-white-50 mb-1">Gestiona el registro y salida de pacientes del Sanatorio</p>
                <small class="text-white-50">
                    <i class="fas fa-calendar-alt mr-1"></i>{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </small>
            </div>
            <img src="{{ asset(config('clinica.icono')) }}" alt="{{ config('clinica.nombre_largo') }}"
                 style="height:60px;opacity:0.25;" onerror="this.style.display='none'">
        </div>
    </div>

    {{-- Tarjetas stats --}}
    <div class="row mb-4">
        <div class="col-12 col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid var(--eden-medio);border-radius:10px;border-top:none;border-right:none;border-bottom:none;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:1px;">PACIENTES HOY</p>
                        <h3 class="font-weight-bold mb-0" style="color:var(--eden-oscuro)">{{ $pacientesHoy ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(45,95,138,0.12)">
                        <i class="fas fa-users" style="color:var(--eden-medio);font-size:20px"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid var(--eden-naranja);border-radius:10px;border-top:none;border-right:none;border-bottom:none;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:1px;">PENDIENTES DE ALTA</p>
                        <h3 class="font-weight-bold mb-0" style="color:var(--eden-oscuro)">{{ $hospitalizacionesPendientes ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(230,126,34,0.12)">
                        <i class="fas fa-exclamation-triangle" style="color:var(--eden-naranja);font-size:20px"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3">
            <div class="card h-100" style="border-left:4px solid var(--eden-rojo);border-radius:10px;border-top:none;border-right:none;border-bottom:none;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:1px;">FACTURAS PENDIENTES</p>
                        <h3 class="font-weight-bold mb-0" style="color:var(--eden-oscuro)">{{ $facturasPendientes ?? 0 }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:50px;height:50px;background:rgba(192,57,43,0.12)">
                        <i class="fas fa-file-invoice" style="color:var(--eden-rojo);font-size:20px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pacientes listos para salida --}}
    <div class="card mb-4" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:white;border-bottom:2px solid var(--eden-naranja);border-radius:10px 10px 0 0">
            <div class="d-flex align-items-center">
                <i class="fas fa-sign-out-alt mr-2" style="color:var(--eden-naranja)"></i>
                <strong>Pacientes Listos para Salida</strong>
            </div>
            <span class="badge badge-warning">{{ $pacientesListosParaSalida->count() }} pendientes</span>
        </div>
        <div class="card-body p-0">
            @if($pacientesListosParaSalida->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>PACIENTE</th>
                            <th>DNI</th>
                            <th>FACTURA</th>
                            <th>MONTO PAGADO</th>
                            <th>HORA PAGO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pacientesListosParaSalida as $item)
                        <tr>
                            <td>{{ $item->paciente->nombres ?? '—' }} {{ $item->paciente->apellidos ?? '' }}</td>
                            <td>{{ $item->paciente->dni ?? '—' }}</td>
                            <td>{{ $item->numero_factura }}</td>
                            <td style="color:var(--eden-verde);font-weight:600">${{ number_format($item->total, 0, ',', '.') }}</td>
                            <td>{{ $item->fecha_pago ? $item->fecha_pago->format('H:i') : '--:--' }}</td>
                            <td>
                                <a href="{{ route('recepcion.pacientes.salida', $item->paciente->id) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-check mr-1"></i> Dar Salida
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#ccc"></i>
                No hay pacientes pendientes de salida
            </div>
            @endif
        </div>
    </div>

    {{-- Acciones Rápidas --}}
    <div class="card mb-4" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="card-header" style="background:white;border-bottom:1px solid #eee;border-radius:10px 10px 0 0">
            <strong>Acciones Rápidas</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-4 mb-3">
                    <a href="{{ route('recepcion.pacientes.index') }}" class="text-decoration-none">
                        <div class="card text-center py-3 h-100"
                             style="border:2px solid rgba(45,95,138,0.2);border-radius:10px;transition:all 0.2s"
                             onmouseover="this.style.borderColor='var(--eden-medio)'"
                             onmouseout="this.style.borderColor='rgba(45,95,138,0.2)'">
                            <i class="fas fa-user-plus fa-2x mb-2" style="color:var(--eden-medio)"></i>
                            <p class="font-weight-bold mb-0" style="color:var(--eden-medio)">Gestión de Pacientes</p>
                            <small class="text-muted">Registrar, editar y gestionar</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <a href="{{ route('recepcion.salidas') }}" class="text-decoration-none">
                        <div class="card text-center py-3 h-100"
                             style="border:2px solid rgba(39,174,96,0.2);border-radius:10px;transition:all 0.2s"
                             onmouseover="this.style.borderColor='var(--eden-verde)'"
                             onmouseout="this.style.borderColor='rgba(39,174,96,0.2)'">
                            <i class="fas fa-sign-out-alt fa-2x mb-2" style="color:var(--eden-verde)"></i>
                            <p class="font-weight-bold mb-0" style="color:var(--eden-verde)">Procesar Salidas</p>
                            <small class="text-muted">Gestionar salida de pacientes</small>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <a href="#historial-pacientes" class="text-decoration-none">
                        <div class="card text-center py-3 h-100"
                             style="border:2px solid rgba(230,126,34,0.2);border-radius:10px;transition:all 0.2s"
                             onmouseover="this.style.borderColor='var(--eden-naranja)'"
                             onmouseout="this.style.borderColor='rgba(230,126,34,0.2)'">
                            <i class="fas fa-book-medical fa-2x mb-2" style="color:var(--eden-naranja)"></i>
                            <p class="font-weight-bold mb-0" style="color:var(--eden-naranja)">Base de Pacientes</p>
                            <small class="text-muted">Historial — reactivar previos</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Historial Pacientes Livewire --}}
    <div id="historial-pacientes" class="card mb-4"
         style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="card-header d-flex justify-content-between align-items-center"
             style="background:white;border-bottom:2px solid var(--eden-medio);border-radius:10px 10px 0 0">
            <div class="d-flex align-items-center">
                <i class="fas fa-history mr-2" style="color:var(--eden-medio)"></i>
                <strong>Todos los Pacientes Registrados</strong>
            </div>
        </div>
        <div class="card-body">
            @livewire('recepcion.historial-pacientes')
        </div>
    </div>

    {{-- Notificaciones Recientes --}}
    <div class="card mb-4" style="border-radius:10px;border:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="card-header" style="background:white;border-bottom:1px solid #eee;border-radius:10px 10px 0 0">
            <strong><i class="fas fa-bell mr-2" style="color:var(--eden-medio)"></i>Notificaciones Recientes</strong>
        </div>
        <div class="card-body p-0">
            @forelse($notificaciones as $notificacion)
            <div class="d-flex align-items-start p-3 border-bottom">
                <div class="mr-3 mt-1">
                    <span class="d-inline-block rounded-circle" style="width:8px;height:8px;background:var(--eden-medio)"></span>
                </div>
                <div class="flex-grow-1">
                    <p class="mb-0 font-weight-bold" style="font-size:13px">{{ $notificacion->titulo }}</p>
                    <p class="mb-0 text-muted" style="font-size:12px">{{ Str::limit($notificacion->mensaje, 100) }}</p>
                    <small class="text-muted">{{ $notificacion->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @empty
            <div class="text-center py-3 text-muted">
                <small>No hay notificaciones recientes</small>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
