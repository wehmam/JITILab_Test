<?php

namespace App\Enums\Tokens;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;

/**
 * The TokenAbility enum represents the abilities associated with a token.
 *
 * @package App\Enums\Tokens
 * @author  Imam Maulana Ashari <https://github.com/wehmam>
 * */
enum TokenAbility: string
{
    case SESSION_REFRESH = 'session:refresh';

    case BLOG_CREATE = 'blog:create';
    case BLOG_VIEW = 'blog:view';
    case BLOG_UPDATE = 'blog:update';
    case BLOG_DELETE = 'blog:delete';

    /**
     * Generates an access token.
     *
     * @return array
     */
    public static function accessToken(): array
    {
        return Cache::remember(
            key: TokenAbility::class . '_accessToken',
            ttl: CarbonImmutable::now()->addMinute(),
            callback: function () {
                $data = [];
                foreach (self::cases() as $case) {
                    if ($case != self::SESSION_REFRESH) {
                        $data[] = $case->value;
                    }
                }

                return $data;
            });
    }

    /**
     * Refreshes the token and returns an array.
     *
     * @return array
     */
    public static function refreshToken(): array
    {
        return Cache::remember(
            key: TokenAbility::class . '_refreshToken',
            ttl: CarbonImmutable::now()->addMinute(),
            callback: function () {
                return [self::SESSION_REFRESH->value];
            }
        );
    }
}
