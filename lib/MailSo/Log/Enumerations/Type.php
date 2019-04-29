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
	const INFO = 0;
	const NOTICE = 1;
	const WARNING = 2;
	const ERROR = 3;
	const SECURE = 4;
	const NOTE = 5;
	const TIME = 6;
	const MEMORY = 7;
	const TIME_DELTA = 8;

	const NOTICE_PHP = 11;
	const WARNING_PHP = 12;
	const ERROR_PHP = 13;
}
