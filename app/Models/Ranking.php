<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'score',
        'analysis',
    ];

    protected $casts = [
        'analysis' => 'array', // 🔑 convierte JSON de DB a array en PHP
    ];

    /**
     * Relación con Application
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
