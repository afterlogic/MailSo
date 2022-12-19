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
    public const USER = 0;
    public const INBOX = 1;
    public const SENT = 2;
    public const DRAFTS = 3;
    public const JUNK = 4;
    public const TRASH = 5;
    public const IMPORTANT = 10;
    public const FLAGGED = 11;
    public const ALL = 12;
}
