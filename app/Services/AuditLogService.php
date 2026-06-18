<?php
namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(
        string $activity,
        string $module,
        array  $newData  = [],
        array  $oldData  = [],
    ): void {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'activity'   => $activity,
            'module'     => $module,
            'old_data'   => !empty($oldData) ? $oldData : null,
            'new_data'   => !empty($newData) ? $newData : null,
            'ip_address' => request()->ip(),
        ]);
    }
}