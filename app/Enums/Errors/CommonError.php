<?php

namespace App\Enums\Errors;

/**
 * The CommonError enum represents common error codes in the application.
 *
 * @package App\Enums\Errors
 * @author  Imam Maulana Ashari <https://github.com/wehmam>
 * */
enum CommonError: string
{
    /**
     * Use in every API
     *
     *
     * */
    case ERR_BAD_REQUEST = 'ERR_BAD_REQUEST';
    case ERR_INVALID_ACCESS_TOKEN = 'ERR_INVALID_ACCESS_TOKEN';
    case ERR_FORBIDDEN_ACCESS = 'ERR_FORBIDDEN_ACCESS';
    case ERR_NOT_FOUND = 'ERR_NOT_FOUND';
    case ERR_INTERNAL_ERROR = 'ERR_INTERNAL_ERROR';

    /**
     * Only used in Login & Refresh Access Token
     *
     *
     *
     * */
    case ERR_INVALID_CREDS = 'ERR_INVALID_CREDS';
    case ERR_INVALID_REFRESH_TOKEN = 'ERR_INVALID_REFRESH_TOKEN';

    /**
     * Generates an instance of the class based on the given HTTP status code.
     * for detail, see: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
     *
     * @param int $httpStatusCode The HTTP status code to generate the instance from.
     * @return self|null Returns an instance of the class based on the HTTP status code, or null if no match is found.
     */
    public static function createFromHttpStatusCode(int $httpStatusCode): self|null
    {
        return match ($httpStatusCode) {
            400 => self::ERR_BAD_REQUEST,
            401 => self::ERR_INVALID_ACCESS_TOKEN,
            403 => self::ERR_FORBIDDEN_ACCESS,
            404 => self::ERR_NOT_FOUND,
            500 => self::ERR_INTERNAL_ERROR,
            default => null
        };
    }

    /**
     * Converts the current object to an associative array.
     *
     *
     * @param string|null $message The custom message to include in the string or null. If not provided, the default message will be used.
     * @return array The associative array representation of the object.
     */
    public function toMap(string|null $message = null): array
    {
        $data = [];
        if ($this->httpStatusCode() == 500) {
            $data['status'] = 500;
        } else {
            $data['status'] = false;
        }

        return array_merge($data, ['error' => $this->value, 'message' => ($message ?? $this->message())]);
    }

    /**
     * Returns the HTTP status code for the given error constant.
     *
     *
     *
     *
     * @return int
     */
    public function httpStatusCode(): int
    {
        return match ($this) {
            self::ERR_BAD_REQUEST => 400,
            self::ERR_INVALID_ACCESS_TOKEN,
            self::ERR_INVALID_CREDS,
            self::ERR_INVALID_REFRESH_TOKEN => 401,
            self::ERR_FORBIDDEN_ACCESS => 403,
            self::ERR_NOT_FOUND => 404,
            self::ERR_INTERNAL_ERROR => 500,
        };
    }

    /**
     * Retrieves the appropriate error message based on the current value of `$this`.
     *
     *
     *
     *
     *
     * @return string The error message corresponding to the current value of `$this`.
     */
    public function message(): string
    {
        return match ($this) {
            self::ERR_BAD_REQUEST => 'invalid value of `type`',
            self::ERR_INVALID_ACCESS_TOKEN => 'invalid access token',
            self::ERR_FORBIDDEN_ACCESS => 'user doesn\'t have enough authorization',
            self::ERR_NOT_FOUND => 'resource is not found',
            self::ERR_INTERNAL_ERROR => 'unable to connect into database',
            self::ERR_INVALID_CREDS => 'incorrect username or password',
            self::ERR_INVALID_REFRESH_TOKEN => 'invalid refresh token'
        };
    }
}
