<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base\Enumerations;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Base
 * @subpackage Enumerations
 */
class Encoding
{
    public const QUOTED_PRINTABLE = 'Quoted-Printable';
    public const QUOTED_PRINTABLE_LOWER = 'quoted-printable';
    public const QUOTED_PRINTABLE_SHORT = 'Q';

    public const BASE64 = 'Base64';
    public const BASE64_LOWER = 'base64';
    public const BASE64_SHORT = 'B';

    public const SEVEN_BIT = '7bit';
    public const _7_BIT = '7bit';
    public const EIGHT_BIT = '8bit';
    public const _8_BIT = '8bit';
}
