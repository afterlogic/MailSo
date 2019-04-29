<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Mime\Parser;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Mime
 * @subpackage Parser
 */
class ParserEmpty implements ParserInterface
{
	/**
	 * @param \MailSo\Mime\Part $oPart
	 *
	 * @return void
	 */
	public function StartParse(\MailSo\Mime\Part &$oPart)
	{
	}

	/**
	 * @param \MailSo\Mime\Part $oPart
	 *
	 * @return void
	 */
	public function EndParse(\MailSo\Mime\Part &$oPart)
	{
	}

	/**
	 * @param \MailSo\Mime\Part $oPart
	 *
	 * @return void
	 */
	public function StartParseMimePart(\MailSo\Mime\Part &$oPart)
	{
	}

	/**
	 * @param \MailSo\Mime\Part $oMimePart
	 *
	 * @return void
	 */
	public function EndParseMimePart(\MailSo\Mime\Part &$oPart)
	{
	}

	/**
	 * @return void
	 */
	public function InitMimePartHeader()
	{
	}

	/**
	 * @param string $sBuffer
	 *
	 * @return void
	 */
	public function ReadBuffer($sBuffer)
	{
	}

	/**
	 * @param string $sBuffer
	 *
	 * @return void
	 */
	public function WriteBody($sBuffer)
	{
	}
}
