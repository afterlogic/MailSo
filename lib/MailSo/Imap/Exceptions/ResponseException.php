<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Imap\Exceptions;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Imap
 * @subpackage Exceptions
 */
class ResponseException extends \MailSo\Imap\Exceptions\Exception
{
	/**
	 * @var array
	 */
	private $aResponses;

	/**
	 * @param array $aResponses = array
	 * @param string $sMessage = ''
	 * @param int $iCode = 0
	 * @param \Exception $oPrevious = null
	 */
	public function __construct($aResponses = array(), $sMessage = '', $iCode = 0, $oPrevious = null)
	{
		if (is_array($aResponses))
		{
			$this->aResponses = $aResponses;
			
			if (empty($sMessage))
			{
				foreach ($this->GetResponses() as $oResponse)
				{
					$sMessage .= $oResponse->ToLine();
				}
			}
		}
		
		parent::__construct($sMessage, $iCode, $oPrevious);

	}

	/**
	 * @return array
	 */
	public function GetResponses()
	{
		return $this->aResponses;
	}

	/**
	 * @return \MailSo\Imap\Response|null
	 */
	public function GetLastResponse()
	{
		return 0 < count($this->aResponses) ? $this->aResponses[count($this->aResponses) - 1] : null;
	}
}
