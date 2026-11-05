<?php

namespace App\Support;

use App\Models\Setting;

final class EmailVerification
{
    public const SETTING_KEY = 'email_verification_enabled';

    public static function enabled(): bool
    {
        return filter_var(
            Setting::query()
                ->where('key', self::SETTING_KEY)
                ->value('value') ?? true,
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
