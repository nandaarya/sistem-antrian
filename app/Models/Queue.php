<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'service_id',
        'admin_id',
        'queue_number',
        'status',
        'queue_date',
        'created_at',
        'called_at',
        'completed_at',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
