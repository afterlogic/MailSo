<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Base
 */
class DateTimeHelper
{
	/**
	 * @access private
	 */
	private function __construct()
	{
	}

	/**
	 * @staticvar \DateTimeZone $oDateTimeZone
	 *
	 * @return \DateTimeZone
	 */
	public static function GetUtcTimeZoneObject()
	{
		static $oDateTimeZone = null;
		if (null === $oDateTimeZone)
		{
			$oDateTimeZone = new \DateTimeZone('UTC');
		}
		return $oDateTimeZone;
	}

	/**
	 * Parse date string formated as "Thu, 10 Jun 2010 08:58:33 -0700 (PDT)"
	 * RFC2822
	 *
	 * @param string $sDateTime
	 *
	 * @return int
	 */
	public static function ParseRFC2822DateString($sDateTime)
	{
		$sDateTime = \trim(\preg_replace('/ \([a-zA-Z0-9]+\)$/', '', \trim($sDateTime)));
		$oDateTime = \DateTime::createFromFormat('D, d M Y H:i:s O', $sDateTime, \MailSo\Base\DateTimeHelper::GetUtcTimeZoneObject());
		if (!$oDateTime)
		{
			$oDateTime = \DateTime::createFromFormat('d M Y H:i:s O', $sDateTime, \MailSo\Base\DateTimeHelper::GetUtcTimeZoneObject());
		}
		return $oDateTime ? $oDateTime->getTimestamp() : 0;
	}

	/**
	 * Parse date string formated as "10-Jan-2012 01:58:17 -0800"
	 * IMAP INTERNALDATE Format
	 *
	 * @param string $sDateTime
	 *
	 * @return int
	 */
	public static function ParseInternalDateString($sDateTime)
	{
		$sDateTime = \trim($sDateTime);
		if (\preg_match('/^[a-z]{2,4}, /i', $sDateTime)) // RFC2822
		{
			return \MailSo\Base\DateTimeHelper::ParseRFC2822DateString($sDateTime);
		}

		return self::ParseDateStringType1($sDateTime);
	}

	/**
	 * Parse date string formated as "2011-06-14 23:59:59 +0400"
	 *
	 * @param string $sDateTime
	 *
	 * @return int
	 */
	public static function ParseDateStringType1($sDateTime)
	{
		$oDateTime = \DateTime::createFromFormat('d-M-Y H:i:s O', $sDateTime, \MailSo\Base\DateTimeHelper::GetUtcTimeZoneObject());
		if (!$oDateTime)
		{
			$oDateTime = \DateTime::createFromFormat('d-M-Y H:i:s O T', $sDateTime, \MailSo\Base\DateTimeHelper::GetUtcTimeZoneObject());
		}
		return $oDateTime ? $oDateTime->getTimestamp() : 0;
	}
}
