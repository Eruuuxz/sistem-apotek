<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    public function detail()
{
    return $this->hasMany(ReturDetail::class);
}

}
