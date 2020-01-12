<?php

/**
 * PHP version 7
 *
 * This is the Tochka Bank API PHP wrapper
 *
 * @category Tochka_API
 * @package  tochka-api-php
 * @author   Andrey Zagoruyko <andrey@azartel.ru>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/azagoru/tochka-api-php
 */

namespace Azagoru\TochkaApiPHP;

class Resources
{
    public static $Token            = ['oauth2', 'token'];
    public static $Statement        = ['statement'];
    public static $StatementResult  = ['statement', 'result'];
    public static $StatementStatus  = ['statement', 'status'];
    public static $OrganizationList = ['organization', 'list'];
    public static $AccountList      = ['account', 'list'];
}
