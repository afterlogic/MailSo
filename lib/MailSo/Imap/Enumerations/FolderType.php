<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Imap\Enumerations;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Imap
 * @subpackage Enumerations
 */
class FolderType
{
	const USER = 0;
	const INBOX = 1;
	const SENT = 2;
	const DRAFTS = 3;
	const JUNK = 4;
	const TRASH = 5;
	const IMPORTANT = 10;
	const FLAGGED = 11;
	const ALL = 12;
}
