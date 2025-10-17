<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait LogsActivityWithIp
{
    use LogsActivity;

    /**
     * Customize activity log to include IP address and user agent
     * This method is called by Spatie ActivityLog package
     */
    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity, string $eventName)
    {
        // Get IP address from request
        $ipAddress = request()->ip();
        
        // Get user agent
        $userAgent = request()->userAgent();
        
        // Add IP and user agent to properties
        $activity->properties = $activity->properties->merge([
            'ip' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }
}
