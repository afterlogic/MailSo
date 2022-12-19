<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Log\Enumerations;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Log
 * @subpackage Enumerations
 */
class Type
{
    public const INFO = 0;
    public const NOTICE = 1;
    public const WARNING = 2;
    public const ERROR = 3;
    public const SECURE = 4;
    public const NOTE = 5;
    public const TIME = 6;
    public const MEMORY = 7;
    public const TIME_DELTA = 8;

    public const NOTICE_PHP = 11;
    public const WARNING_PHP = 12;
    public const ERROR_PHP = 13;
}
