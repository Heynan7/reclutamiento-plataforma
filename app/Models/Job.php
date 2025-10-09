<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'image',
        'is_open',
    ];

    protected $casts = [
        'is_open'    => 'boolean',
        'deleted_at' => 'datetime', // no es estrictamente necesario, pero es claro
    ];

    // Valor por defecto al crear
    protected $attributes = [
        'is_open' => true,
    ];

    /* =========================
     | Relaciones
     ==========================*/
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /* =========================
     | Scopes útiles
     ==========================*/
    // Activas (abiertas)
    public function scopeActive($q)
    {
        return $q->where('is_open', true);
    }

    // Alias por compatibilidad con lo que ya tenías
    public function scopeOpen($q)
    {
        return $this->scopeActive($q);
    }

    // Cerradas
    public function scopeClosed($q)
    {
        return $q->where('is_open', false);
    }

    // Ocultar vacantes donde ya aplicó un usuario
    public function scopeVisibleFor($q, ?int $userId)
    {
        if (!$userId) return $q; // invitados ven todo lo activo
        return $q->whereDoesntHave('applications', function ($sub) use ($userId) {
            $sub->where('user_id', $userId);
        });
    }

    /* =========================
     | Helpers (opcional)
     ==========================*/
    public function hasApplied(int $userId): bool
    {
        return $this->applications()
            ->where('user_id', $userId)
            ->exists();
    }
}
