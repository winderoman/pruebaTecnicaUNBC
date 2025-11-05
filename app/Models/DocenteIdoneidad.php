<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DocenteIdoneidad extends Model
{
    use HasFactory;

    protected $table = 'docentes_idoneidad';

    protected $fillable = [
        'institucion_id',
        'doc_run',
        'doc_dv',
        'doc_nombre',
        'doc_paterno',
        'doc_materno',
        'doc_genero',
        'doc_fec_nac',
        'rbd',
        'nombre_esta',
        'funcion_principal',
        'funcion_secundaria',
        'horas_contrato',
        'horas_ingles',
        'horas_directivas',
        'horas_tecnico_pedagogica',
        'horas_aula',
        'horas_pedagogicas_funcion_1',
        'horas_pedagogicas_funcion_2',
        'sector_funcion_1',
        'sub_sector_funcion_1',
        'sector_funcion_2',
        'sub_sector_funcion_2',
        'estado_idoneidad',
        'fecha_carga',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'doc_fec_nac' => 'date',
        'fecha_carga' => 'date',
        'fecha_actualizacion' => 'datetime',
        'horas_contrato' => 'integer',
        'horas_ingles' => 'integer',
        'horas_directivas' => 'integer',
        'horas_tecnico_pedagogica' => 'integer',
        'horas_aula' => 'integer',
        'horas_pedagogicas_funcion_1' => 'integer',
        'horas_pedagogicas_funcion_2' => 'integer',
    ];

    /**
     * Relación: Un docente pertenece a una institución
     */
    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * Accessor: RUT completo con formato
     */
    public function getRutCompletoAttribute()
    {
        return $this->doc_run . '-' . $this->doc_dv;
    }

    /**
     * Accessor: Nombre completo
     */
    public function getNombreCompletoAttribute()
    {
        return trim($this->doc_nombre . ' ' . $this->doc_paterno . ' ' . $this->doc_materno);
    }

    /**
     * Crear o actualizar docente (evita duplicados)
     */
    public static function createOrUpdate(array $data): self
    {
        $fechaCarga = $data['fecha_carga'] ?? Carbon::now()->format('Y-m-d');
        
        $docente = self::updateOrCreate(
            [
                'institucion_id' => $data['institucion_id'],
                'doc_run' => $data['doc_run'],
                'doc_dv' => $data['doc_dv'],
                'fecha_carga' => $fechaCarga,
            ],
            array_merge($data, [
                'fecha_actualizacion' => Carbon::now(),
            ])
        );

        return $docente;
    }

    /**
     * Scope: Filtrar por estado de idoneidad
     */
    public function scopeConEstado($query, $estado)
    {
        return $query->where('estado_idoneidad', $estado);
    }

    /**
     * Scope: Filtrar por RBD
     */
    public function scopeDeRbd($query, $rbd)
    {
        return $query->where('rbd', $rbd);
    }

    /**
     * Scope: Filtrar por fecha de carga
     */
    public function scopeDeFechaCarga($query, $fecha)
    {
        return $query->whereDate('fecha_carga', $fecha);
    }
}