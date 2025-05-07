<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Goal extends Model implements Auditable
{
    use ClearsResponseCache, HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'capacitaciones_db';
    protected $table = 'goals';

    protected $guarded = ['id'];

    protected $fillable = [
        'id',
        'name',
        'course_id',
    ];

    // Relacion uno a muchos inversa
    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
