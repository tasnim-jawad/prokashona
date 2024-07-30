<?php

namespace App\Models\Order;

use App\Models\Payment\UserPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    protected $guarded = [];

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

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
    public function attachment()
    {
        return $this->hasOne(OrderPaymentAttachment::class,'order_payment_id');
    }

    public function order_user_payment()
    {
        return $this->belongsTo(UserPayment::class,'user_payment_id');
    }
}
