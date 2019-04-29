<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 */
final class Version
{
	/**
	 * @var string
	 */
	const APP_VERSION = '1.3.3';

	/**
	 * @var string
	 */
	const MIME_X_MAILER = 'MailSo';

	/**
	 * @return string
	 */
	public static function AppVersion()
	{
		return \MailSo\Version::APP_VERSION;
	}

	/**
	 * @return string
	 */
	public static function XMailer()
	{
		return \MailSo\Version::MIME_X_MAILER.'/'.\MailSo\Version::APP_VERSION;
	}

	/**
	 * @return string
	 */
	public static function Signature()
	{
		$sSignature = '';
		if (\defined('MAILSO_LIBRARY_USE_PHAR'))
		{
			$oPhar = new \Phar('mailso.phar');
			$sSignature = $oPhar->getSignature();
		}
		
		return $sSignature;
	}
}
