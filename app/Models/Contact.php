<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Contact extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        foreach (['contacts', 'contact_messages'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return parent::getTable();
    }
}
