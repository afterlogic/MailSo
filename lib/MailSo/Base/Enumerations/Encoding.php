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
	const QUOTED_PRINTABLE = 'Quoted-Printable';
	const QUOTED_PRINTABLE_LOWER = 'quoted-printable';
	const QUOTED_PRINTABLE_SHORT = 'Q';

	const BASE64 = 'Base64';
	const BASE64_LOWER = 'base64';
	const BASE64_SHORT = 'B';

	const SEVEN_BIT = '7bit';
	const _7_BIT = '7bit';
	const EIGHT_BIT = '8bit';
	const _8_BIT = '8bit';
}
