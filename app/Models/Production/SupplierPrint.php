<?php

namespace App\Models\Production;

use App\Models\Account\AccountSupplierLog;
use App\Models\User\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPrint extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = [
        "mobile_numbers",
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($data) {
            if (isset($data->name)) {
                $data->slug = \Illuminate\Support\Str::slug($data->name);
            } else if (isset($data->title)) {
                $data->slug = \Illuminate\Support\Str::slug($data->title);
            } else {
                $data->slug = $data->id . uniqid();
            }
            if (auth()->check()) {
                $data->creator = auth()->user()->id;
            }
        });
    }

    public function getMobileNumbersAttribute()
    {
        return PhoneNumber::select(['id','table_id','table_name','mobile_number'])->where('table_id',$this->id)->where('table_name','supplier_prints')->get();
    }

    public function logs()
    {
        return $this->hasMany(AccountSupplierLog::class,'supplier_id');
    }
}
