<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleFactura extends Model
{
    use HasFactory;

    protected $table = 'detalle_facturas';

    protected $fillable = [
        'factura_id',
        'tarifa_id',
        'concepto',
        'tipo',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'cantidad'        => 'integer',
    ];

    // ── Accessors ────────────────────────────────────────────

    public function getPrecioUnitarioFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->precio_unitario, 0, ',', '.');
    }

    public function getSubtotalFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->subtotal, 0, ',', '.');
    }

    // ── Relaciones ───────────────────────────────────────────

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function tarifa(): BelongsTo
    {
        return $this->belongsTo(Tarifa::class);
    }
}
