<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesData extends Model
{
    use HasFactory;

    protected $fillable =[
        'id_cpm',
        'penerus',
        'izin',
        'early_start',
        'early_finish',
        'late_start',
        'late_finsih',
        'kode'
    ];

    /**
     * Get the DataCpm that owns the ProsesData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cpm()
    {
        return $this->belongsTo(DataCpm::class, 'id_cpm', 'id');
    }
}
