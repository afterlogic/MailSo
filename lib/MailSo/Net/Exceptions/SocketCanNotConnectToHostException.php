<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Net\Exceptions;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Net
 * @subpackage Exceptions
 */
class SocketCanNotConnectToHostException extends \MailSo\Net\Exceptions\ConnectionException
{
	/**
	 * @var string
	 */
	private $sSocketMessage;

	/**
	 * @var int
	 */
	private $iSocketCode;

	/**
	 * @param string $sSocketMessage = ''
	 * @param int $iSocketCode = 0
	 * @param string $sMessage = ''
	 * @param int $iCode = 0
	 * @param \Exception $oPrevious = null
	 */
	public function __construct($sSocketMessage = '', $iSocketCode = 0, $sMessage = '', $iCode = 0, $oPrevious = null)
	{
		parent::__construct($sMessage, $iCode, $oPrevious);

		$this->sSocketMessage = $sSocketMessage;
		$this->iSocketCode = $iSocketCode;
	}

	/**
	 * @return string
	 */
	public function getSocketMessage()
	{
		return $this->sSocketMessage;
	}

	/**
	 * @return int
	 */
	public function getSocketCode()
	{
		return $this->iSocketCode;
	}
}
