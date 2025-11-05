<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';

    protected $fillable = [
        'rbd',
        'nombre',
    ];

    /**
     * Relación: Una institución tiene muchos docentes
     */
    public function docentes()
    {
        return $this->hasMany(DocenteIdoneidad::class);
    }

    /**
     * Buscar o crear institución por RBD
     */
    public static function findOrCreateByRbd(string $rbd, string $nombre): self
    {
        return self::firstOrCreate(
            ['rbd' => $rbd],
            ['nombre' => $nombre]
        );
    }
}