<?php

namespace Modules\CapacitacionesCore\App\Models\Escuela;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Image extends Model implements Auditable
{
    use ClearsResponseCache, HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'capacitaciones_db';
    protected $table = 'images';

    protected $guarded = ['id'];

    public function imageable()
    {
        return $this->morphTo();
    }
}
