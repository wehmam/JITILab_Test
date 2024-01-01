<?php

namespace App\Enums\Tokens;

/**
 * The TokenName enum represents the name of the token.
 *
 * @package App\Enums\Tokens
 * @author  Imam Maulana Ashari <https://github.com/wehmam>
 * */
enum TokenName: string
{
    case ACCESS_TOKEN = 'access_token';
    case REFRESH_TOKEN = 'refresh_token';

    /**
     * Retrieves the abilities associated with the current token.
     *
     * @return array The array of abilities.
     */
    public function getAbilities(): array
    {
        return match ($this) {
            self::ACCESS_TOKEN => TokenAbility::accessToken(),
            self::REFRESH_TOKEN => TokenAbility::refreshToken(),
        };
    }
}
