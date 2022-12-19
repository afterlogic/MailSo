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
class StoreAction
{
    public const SET_FLAGS = 'FLAGS';
    public const SET_FLAGS_SILENT = 'FLAGS.SILENT';
    public const ADD_FLAGS = '+FLAGS';
    public const ADD_FLAGS_SILENT = '+FLAGS.SILENT';
    public const REMOVE_FLAGS = '-FLAGS';
    public const REMOVE_FLAGS_SILENT = '-FLAGS.SILENT';

    public const SET_GMAIL_LABELS = 'X-GM-LABELS';
    public const SET_GMAIL_LABELS_SILENT = 'X-GM-LABELS.SILENT';
    public const ADD_GMAIL_LABELS = '+X-GM-LABELS';
    public const ADD_GMAIL_LABELS_SILENT = '+X-GM-LABELS.SILENT';
    public const REMOVE_GMAIL_LABELS = '-X-GM-LABELS';
    public const REMOVE_GMAIL_LABELS_SILENT = '-X-GM-LABELS.SILENT';
}
