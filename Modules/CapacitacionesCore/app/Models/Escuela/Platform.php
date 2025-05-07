<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Platform extends Model implements Auditable
{
    use ClearsResponseCache, HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $connection = 'capacitaciones_db';
    protected $table = 'platforms';

    protected $guarded = ['id'];

    // Relacion uno a muchos
    public function lessons()
    {
        return $this->hasMany('App\Models\Lesson');
    }
}
