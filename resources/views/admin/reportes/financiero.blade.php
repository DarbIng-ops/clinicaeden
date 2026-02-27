@extends('layouts.adminlte')

@section('title', 'Reporte Financiero')
@section('page-title', 'Reporte Financiero')

@push('styles')
<style>
    @media print {
        .main-sidebar, .main-header, .content-header, .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; padding-top: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .print-header { display: block !important; }
    }
    .print-header { display: none; }
</style>
@endpush

@section('content')

    <div class="print-header text-center mb-4">
        <h2>Clínica Eden — Reporte Financiero</h2>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        <hr>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('admin.reportes') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
        </button>
    </div>

    <div class="card card-outline card-danger no-print">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ingresos Cobrados</span>
                    <span class="info-box-number">$ {{ number_format($totalIngresos, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hourglass-half"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Por Cobrar</span>
                    <span class="info-box-number">$ {{ number_format($totalPendiente, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file-invoice-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Facturas</span>
                    <span class="info-box-number">{{ $facturas->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Facturas del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>N° Factura</th>
                        <th>Paciente</th>
                        <th>Fecha Emisión</th>
                        <th class="text-right">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $i => $factura)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $factura->numero_factura ?? '#' . $factura->id }}</strong></td>
                        <td>{{ optional($factura->paciente)->nombre_completo ?? '—' }}</td>
                        <td>{{ $factura->created_at->format('d/m/Y') }}</td>
                        <td class="text-right font-weight-bold">
                            $ {{ number_format($factura->total, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $factura->estado === 'pagado' ? 'success' : ($factura->estado === 'pendiente' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($factura->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay facturas en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($facturas->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="4" class="text-right">Total Cobrado:</th>
                        <th class="text-right text-success">$ {{ number_format($totalIngresos, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right">Por Cobrar:</th>
                        <th class="text-right text-warning">$ {{ number_format($totalPendiente, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@endsection
