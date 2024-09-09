<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    /**
     * Get the service associated with the Guest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service');
    }

    // DIPAKE DI DASHBOARD KARENA ADA FIELD JUGA DENGAN NAMA service JADI DIBUAT LAGI BIAR GA BENTROK
    public function services()
    {
        return $this->belongsTo(Service::class, 'service');
    }

    /**
     * Get the serviceLainnya associated with the Guest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function serviceLainnya()
    {
        return $this->hasOne(ServiceLainnya::class);
    }
}
