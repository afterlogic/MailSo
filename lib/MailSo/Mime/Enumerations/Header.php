<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Mime\Enumerations;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Mime
 * @subpackage Enumerations
 */
class Header
{
    public const DATE = 'Date';
    public const RECEIVED = 'Received';

    public const SUBJECT = 'Subject';

    public const TO_ = 'To';
    public const FROM_ = 'From';
    public const CC = 'Cc';
    public const BCC = 'Bcc';
    public const REPLY_TO = 'Reply-To';
    public const SENDER = 'Sender';
    public const RETURN_PATH = 'Return-Path';
    public const DELIVERED_TO = 'Delivered-To';

    public const MESSAGE_ID = 'Message-ID';
    public const IN_REPLY_TO = 'In-Reply-To';
    public const REFERENCES = 'References';
    public const X_DRAFT_INFO = 'X-Draft-Info';
    public const X_ORIGINATING_IP = 'X-Originating-IP';

    public const CONTENT_TYPE = 'Content-Type';
    public const CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding';
    public const CONTENT_DISPOSITION = 'Content-Disposition';
    public const CONTENT_DESCRIPTION = 'Content-Description';
    public const CONTENT_ID = 'Content-ID';
    public const CONTENT_LOCATION = 'Content-Location';

    public const SENSITIVITY = 'Sensitivity';

    public const RECEIVED_SPF = 'Received-SPF';
    public const AUTHENTICATION_RESULTS = 'Authentication-Results';
    public const X_DKIM_AUTHENTICATION_RESULTS = 'X-DKIM-Authentication-Results';

    public const DKIM_SIGNATURE = 'DKIM-Signature';
    public const DOMAINKEY_SIGNATURE = 'DomainKey-Signature';

    public const X_SPAM_STATUS = 'X-Spam-Status';

    public const RETURN_RECEIPT_TO = 'Return-Receipt-To';
    public const DISPOSITION_NOTIFICATION_TO = 'Disposition-Notification-To';
    public const X_CONFIRM_READING_TO = 'X-Confirm-Reading-To';

    public const MIME_VERSION = 'Mime-Version';
    public const X_MAILER = 'X-Mailer';

    public const X_MSMAIL_PRIORITY = 'X-MSMail-Priority';
    public const IMPORTANCE = 'Importance';
    public const X_PRIORITY = 'X-Priority';
}
