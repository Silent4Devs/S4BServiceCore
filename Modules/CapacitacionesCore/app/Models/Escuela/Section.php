<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Section extends Model implements Auditable
{
    use ClearsResponseCache, HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'capacitaciones_db';
    protected $table = 'sections';

    protected $guarded = ['id'];

    // Relacion uno a muchos

    public function lessons()
    {
        return $this->hasMany('Modules\CapacitacionesCore\App\Models\Escuela\Lesson')->orderBy('created_at', 'asc');
    }

    // Relacion uno a muchos inversa
    public function course()
    {
        return $this->belongsTo('Modules\CapacitacionesCore\App\Models\Escuela\Course');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'section_id', 'id');
    }
}
