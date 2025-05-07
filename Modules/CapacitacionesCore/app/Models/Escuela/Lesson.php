<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Lesson extends Model implements Auditable
{
    use ClearsResponseCache, HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'capacitaciones_db';
    protected $table = 'lessons';

    protected $guarded = ['id'];

    protected $appends = ['completed', 'completed_user', 'platform_format', 'file_format'];

    // Funcion para indicar a que usuario permanece el avance del curso
    public function getCompletedAttribute()
    {
        return $this->users->contains(auth()->user()->id);
    }

    public function getCompletedUserAttribute($id)
    {
        return $this->users->contains($id);
    }

    public function getPlatformFormatAttribute()
    {
        $platf = Platform::where('id', $this->platform_id)->first();

        return $platf->name;
        //  dd($this->formatType);
    }

    public function getFileFormatAttribute()
    {

        if ($this->resource) {
            $ruta = storage_path('app/' . $this->resource->url);

            // Obtener la extensión del archivo
            $informacionArchivo = pathinfo($ruta);
            $extension = $informacionArchivo['extension'];

            return $extension;
        }
    }

    // Relacion uno a uno

    public function description()
    {
        return $this->hasOne('Modules\CapacitacionesCore\App\Models\Escuela\Description');
    }

    // Relacion uno a muchos inversa
    public function section()
    {
        return $this->belongsTo('Modules\CapacitacionesCore\App\Models\Escuela\Section');
    }

    public function platform()
    {
        return $this->belongsTo('Modules\CapacitacionesCore\App\Models\Escuela\Platform');
    }

    // Relacion muchos a muchos
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    // Relacion uno a uno polimorfica

    public function resource()
    {
        return $this->morphOne('Modules\CapacitacionesCore\App\Models\Escuela\Resource', 'resourceable');
    }

    // Relacion uno a muchos polimorfica

    public function comments()
    {
        return $this->morphMany('Modules\CapacitacionesCore\App\Models\Escuela\Comment', 'commentable');
    }

    public function reactions()
    {
        return $this->morphMany('Modules\CapacitacionesCore\App\Models\Escuela\Reaction', 'reactionable');
    }
}
