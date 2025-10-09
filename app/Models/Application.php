<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'cover_letter',
        'cv_file',
        'job_title_snapshot',

        // Estado general
        'status',
          'status_before_closed',
        'status_updated_at',
      

        // Entrevistas
        'interview_at',
        'interview_channel',
        'interview_link',
        'interview_location',
        'interview_message',

        // Disponibilidad
        'availability_response',
        'availability_confirmed_at',

        // Psychotest
        'psychotest_score',
        'psychotest_answers',
        'psychotest_completed_at',
        'psychotest_link',

        // Estudio socioeconómico
        'socioeconomic_link',
        'socioeconomic_notes',
        'socioeconomic_completed_at',

        // Lectura
        'read_at',
    ];

    protected $casts = [
        'status_updated_at'          => 'datetime',
        'interview_at'               => 'datetime',
        'availability_confirmed_at'  => 'datetime',

        'psychotest_score'           => 'float',
        'psychotest_answers'         => 'array',
        'psychotest_completed_at'    => 'datetime',

        'socioeconomic_completed_at' => 'datetime',
        'read_at'                    => 'datetime',
    ];

    // 📌 Estados del proceso (coherentes con controladores/vistas)
    public const STATUS_SUBMITTED          = 'submitted';
    public const STATUS_SHORTLISTED        = 'shortlisted';
    public const STATUS_INTERVIEW_SCHEDULED= 'interview_scheduled';
    public const STATUS_INTERVIEW_DEEP     = 'interview_deep';
    public const STATUS_PSYCHOTEST         = 'psychotest';
    public const STATUS_SOCIOECONOMIC      = 'socioeconomic_study';
    public const STATUS_HIRED              = 'hired';
    public const STATUS_REJECTED           = 'rejected';
    public const STATUS_CLOSED             = 'closed';
    

    public static function statuses(): array
    {
        return [
            self::STATUS_SUBMITTED,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEW_SCHEDULED,
            self::STATUS_INTERVIEW_DEEP,
            self::STATUS_PSYCHOTEST,
            self::STATUS_SOCIOECONOMIC,
            self::STATUS_HIRED,
            self::STATUS_REJECTED,
            self::STATUS_CLOSED,
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_SUBMITTED           => 'Postulación recibida',
            self::STATUS_SHORTLISTED         => 'Preseleccionado',
            self::STATUS_INTERVIEW_SCHEDULED => 'Entrevista programada',
            self::STATUS_INTERVIEW_DEEP      => 'Entrevista profunda',
            self::STATUS_PSYCHOTEST          => 'Pruebas psicométricas',
            self::STATUS_SOCIOECONOMIC       => 'Estudio socioeconómico',
            self::STATUS_HIRED               => 'Contratado',
            self::STATUS_REJECTED            => 'Rechazado',
            self::STATUS_CLOSED              => 'Cerrado',
        ];
    }

    // 🔗 Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        // mantener withTrashed si Job usa SoftDeletes
        return $this->belongsTo(Job::class)->withTrashed();
    }

    public function ranking()
    {
        return $this->hasOne(Ranking::class);
    }

    // 🏷️ Accesor: etiqueta legible del estado
    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status ?? '');
    }

    // 🔍 Scopes útiles
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_REJECTED, self::STATUS_CLOSED]);
    }

    public function scopeActivePipeline($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEW_SCHEDULED,
            self::STATUS_INTERVIEW_DEEP,
            self::STATUS_PSYCHOTEST,
            self::STATUS_SOCIOECONOMIC,
        ]);
    }

    public function scopePendingPsychotest($query)
    {
        return $query->where('status', self::STATUS_PSYCHOTEST)
                     ->whereNull('psychotest_completed_at');
    }

    public function scopeCompletedPsychotest($query)
    {
        return $query->where('status', self::STATUS_PSYCHOTEST)
                     ->whereNotNull('psychotest_completed_at');
    }

    // 🎯 Accesor opcional para redondeo en listas
    public function getPsychotestScoreRoundedAttribute(): ?int
    {
        return is_null($this->psychotest_score) ? null : (int) round($this->psychotest_score);
    }
}
