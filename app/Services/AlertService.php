<?php
namespace App\Services;

class AlertService
{
    public static function error($message = null)
    {
        notyf()->error($message ? $message : 'Something went wrong.');
    }

    public static function updated($message = null)
    {
        notyf()->success($message ? $message : 'Updated Successfully.');
    }

    public static function created($message = null)
    {
        notyf()->success($message ? $message : 'Created Successfully.');
    }
}
