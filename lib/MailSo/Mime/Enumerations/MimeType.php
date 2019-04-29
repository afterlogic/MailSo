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
class MimeType
{
	const TEXT_PLAIN = 'text/plain';
	const TEXT_HTML = 'text/html';
	
	const MULTIPART_ALTERNATIVE = 'multipart/alternative';
	const MULTIPART_RELATED = 'multipart/related';
	const MULTIPART_MIXED = 'multipart/mixed';
	const MULTIPART_SIGNED = 'multipart/signed';
	
	const MESSAGE_RFC822 = 'message/rfc822';
	const MESSAGE_PARTIAL = 'message/partial';
	const MESSAGE_REPORT = 'message/report';
	
	const APPLICATION_MS_TNEF = 'application/ms-tnef';
	const APPLICATION_PKCS7_MIME = 'application/pkcs7-mime';
	const APPLICATION_PKCS7_SIGNATURE = 'application/pkcs7-signature';
}
