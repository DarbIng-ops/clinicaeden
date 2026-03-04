<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarifa extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria',
        'nombre',
        'precio',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorCategoria($query, string $cat)
    {
        return $query->where('categoria', $cat);
    }

    // ── Accessors ────────────────────────────────────────────

    public function getPrecioFormateadoAttribute(): string
    {
        return '$ ' . number_format($this->precio, 0, ',', '.');
    }

    // ── Relaciones ───────────────────────────────────────────

    public function detallesFactura(): HasMany
    {
        return $this->hasMany(DetalleFactura::class);
    }
}
