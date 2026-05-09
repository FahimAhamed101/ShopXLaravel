<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class WithdrawRequest extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function getTable()
    {
        foreach (['withdraw_requests', 'vendor_withdraw_requests', 'store_withdraw_requests'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return parent::getTable();
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
