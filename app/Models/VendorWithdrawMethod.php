<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class VendorWithdrawMethod extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function getTable()
    {
        foreach (['vendor_withdraw_methods', 'store_withdraw_methods', 'seller_withdraw_methods'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return parent::getTable();
    }

    public function withdrawMethod(): BelongsTo
    {
        return $this->belongsTo(WithdrawMethod::class, 'withdraw_method_id');
    }
}
