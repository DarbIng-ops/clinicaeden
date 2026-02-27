<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->numero_factura ?? $factura->id }} - Clínica Eden</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; font-size: 13px; color: #222; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 20mm; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1e40af; padding-bottom: 15px; margin-bottom: 25px; }
        .logo-area h1 { font-size: 26px; font-weight: bold; color: #1e40af; }
        .logo-area p { color: #555; font-size: 12px; margin-top: 3px; }
        .factura-info { text-align: right; }
        .factura-info .numero { font-size: 20px; font-weight: bold; color: #1e40af; }
        .factura-info .fecha { color: #555; font-size: 12px; margin-top: 5px; }
        .badge-pagado { display: inline-block; background: #d1fae5; color: #065f46; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; margin-top: 8px; }
        .badge-pendiente { display: inline-block; background: #fef3c7; color: #92400e; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; margin-top: 8px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #6b7280; letter-spacing: 0.05em; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        .info-block p { margin-bottom: 5px; }
        .info-block .label { color: #6b7280; font-size: 11px; }
        .info-block .value { font-weight: 500; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #1e40af; color: #fff; padding: 8px 12px; text-align: left; font-size: 12px; }
        tbody td { padding: 9px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tbody tr:last-child td { border-bottom: none; }
        .totals { margin-left: auto; width: 240px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; }
        .total-row.grand { border-top: 2px solid #1e40af; margin-top: 8px; padding-top: 10px; font-size: 16px; font-weight: bold; color: #1e40af; }
        .footer { border-top: 1px solid #e5e7eb; margin-top: 40px; padding-top: 15px; text-align: center; color: #9ca3af; font-size: 11px; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #1e40af; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; }
        @media print {
            .print-btn { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Imprimir</button>

    <div class="page">
        <!-- Encabezado -->
        <div class="header">
            <div class="logo-area">
                <h1>Clínica Eden</h1>
                <p>Sistema de Gestión Hospitalaria</p>
                <p>Tel: (0261) 000-0000 | clinicaeden@ejemplo.com</p>
            </div>
            <div class="factura-info">
                <div class="numero">FACTURA #{{ str_pad($factura->id, 8, '0', STR_PAD_LEFT) }}</div>
                @if($factura->numero_factura)
                    <div class="fecha">{{ $factura->numero_factura }}</div>
                @endif
                <div class="fecha">Emitida: {{ $factura->fecha_emision->format('d/m/Y') }}</div>
                @if($factura->estado === 'pagado')
                    <span class="badge-pagado">PAGADO</span>
                @else
                    <span class="badge-pendiente">PENDIENTE</span>
                @endif
            </div>
        </div>

        <!-- Información del paciente y pago -->
        <div class="info-grid">
            <div class="info-block">
                <div class="section-title">Datos del Paciente</div>
                <p><span class="label">Nombre:</span></p>
                <p class="value">{{ $factura->paciente->nombre_completo }}</p>
                <p style="margin-top:8px"><span class="label">DNI:</span></p>
                <p class="value">{{ $factura->paciente->dni }}</p>
                @if($factura->paciente->telefono)
                <p style="margin-top:8px"><span class="label">Teléfono:</span></p>
                <p class="value">{{ $factura->paciente->telefono }}</p>
                @endif
            </div>

            <div class="info-block">
                <div class="section-title">Información de Pago</div>
                @if($factura->estado === 'pagado')
                    <p><span class="label">Fecha de Pago:</span></p>
                    <p class="value">{{ $factura->fecha_pago ? \Carbon\Carbon::parse($factura->fecha_pago)->format('d/m/Y H:i') : '-' }}</p>
                    <p style="margin-top:8px"><span class="label">Método de Pago:</span></p>
                    <p class="value">{{ ucfirst($factura->metodo_pago ?? '-') }}</p>
                    @if($factura->monto_recibido)
                    <p style="margin-top:8px"><span class="label">Monto Recibido:</span></p>
                    <p class="value">${{ number_format($factura->monto_recibido, 2) }}</p>
                    @endif
                @else
                    <p class="value" style="color:#92400e">Pendiente de pago</p>
                @endif
            </div>
        </div>

        <!-- Detalle del servicio -->
        <div class="section-title">Detalle de Servicios</div>
        <table>
            <thead>
                <tr>
                    <th style="width:50%">Descripción</th>
                    <th>Tipo</th>
                    <th style="text-align:right">Subtotal</th>
                    <th style="text-align:right">Impuestos</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($factura->consulta)
                            Consulta médica
                            @if($factura->consulta->diagnostico)
                                <br><small style="color:#6b7280">Diag: {{ Str::limit($factura->consulta->diagnostico, 60) }}</small>
                            @endif
                        @elseif($factura->hospitalizacion)
                            Hospitalización
                            @if($factura->hospitalizacion->diagnostico_inicial)
                                <br><small style="color:#6b7280">Diag: {{ Str::limit($factura->hospitalizacion->diagnostico_inicial, 60) }}</small>
                            @endif
                        @else
                            Servicio médico
                        @endif
                    </td>
                    <td>
                        @if($factura->consulta) Consulta
                        @elseif($factura->hospitalizacion) Hospitalización
                        @else Otro
                        @endif
                    </td>
                    <td style="text-align:right">${{ number_format($factura->subtotal ?? $factura->total, 2) }}</td>
                    <td style="text-align:right">${{ number_format($factura->impuestos ?? 0, 2) }}</td>
                    <td style="text-align:right">${{ number_format($factura->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totales -->
        <div style="display:flex; justify-content:flex-end">
            <div class="totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>${{ number_format($factura->subtotal ?? $factura->total, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Impuestos:</span>
                    <span>${{ number_format($factura->impuestos ?? 0, 2) }}</span>
                </div>
                <div class="total-row grand">
                    <span>TOTAL:</span>
                    <span>${{ number_format($factura->total, 2) }}</span>
                </div>
                @if($factura->estado === 'pagado' && $factura->monto_recibido)
                <div class="total-row" style="color:#065f46">
                    <span>Cambio:</span>
                    <span>${{ number_format($factura->monto_recibido - $factura->total, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Clínica Eden &mdash; Gracias por confiar en nuestros servicios</p>
            <p style="margin-top:4px">Este documento es un comprobante válido de pago. Consérvelo para sus registros.</p>
            <p style="margin-top:4px">Impreso el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
