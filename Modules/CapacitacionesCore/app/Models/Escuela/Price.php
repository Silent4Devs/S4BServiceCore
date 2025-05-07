<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Price extends Model implements Auditable
{
    use ClearsResponseCache, SoftDeletes;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'capacitaciones_db';
    protected $table = 'prices';

    protected $guarded = ['id'];

    // Relacion uno a muchos
    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }
}
