<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo que representa las facturas emitidas por la clínica.
 *
 * Registra los montos de cobro, métodos de pago, estado y vinculación con
 * pacientes, hospitalizaciones, consultas y personal de caja.
 */
class Factura extends Model
{
    use HasFactory;

    /**
     * @var string Nombre de la tabla asociada
     */
    protected $table = 'facturas';

    /**
     * @var array<int, string> Atributos asignables en masa
     */
    protected $fillable = [
        'numero_factura',
        'paciente_id',
        'hospitalizacion_id',
        'consulta_id',
        'caja_id',
        'subtotal',
        'impuestos',
        'total',
        'metodo_pago',
        'monto_recibido',
        'estado',
        'fecha_emision',
        'fecha_pago',
        'observaciones',
        'observaciones_pago',
    ];

    /**
     * @var array<string, string> Conversión de tipos para atributos
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_emision' => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    // Relaciones
    /**
     * Relación: paciente al que pertenece la factura.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /**
     * Relación: hospitalización asociada (si aplica).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizacion::class);
    }

    /**
     * Relación: consulta asociada (para cobros directos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function consulta()
    {
        return $this->belongsTo(Consulta::class);
    }

    /**
     * Relación: usuario de caja que gestionó el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caja()
    {
        return $this->belongsTo(User::class, 'caja_id');
    }

    // Scopes
    /**
     * Scope para obtener facturas pagadas.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagado');
    }

    /**
     * Scope para facturas pendientes de pago.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para filtrar facturas por paciente.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param int $pacienteId Identificador del paciente
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorPaciente($query, $pacienteId)
    {
        return $query->where('paciente_id', $pacienteId);
    }

    /**
     * Scope para filtrar facturas por fecha de emisión.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string|\DateTimeInterface $fecha Fecha a buscar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_emision', $fecha);
    }

    // Métodos auxiliares
    /**
     * Generar un nuevo número de factura correlativo.
     *
     * @return string Número construido con prefijo FAC-
     */
    public function generarNumeroFactura()
    {
        $ultimaFactura = self::orderBy('id', 'desc')->first();
        $numero = $ultimaFactura ? $ultimaFactura->id + 1 : 1;
        return 'FAC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Marcar una factura como pagada y registrar la fecha de pago.
     *
     * @return void
     */
    public function marcarComoPagada()
    {
        $this->update([
            'estado' => 'pagado',
            'fecha_pago' => now()
        ]);
    }

    /**
     * Recalcular el total de la factura a partir del subtotal e impuestos.
     *
     * @return float Total actualizado
     */
    public function calcularTotal()
    {
        $this->total = $this->subtotal + $this->impuestos;
        $this->save();
        return $this->total;
    }
}
