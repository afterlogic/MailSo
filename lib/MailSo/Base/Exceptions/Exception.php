<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base\Exceptions;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Base
 * @subpackage Exceptions
 */
class Exception extends \Exception
{
	/**
	 * @param string $sMessage
	 * @param int $iCode
	 * @param \Exception|null $oPrevious
	 */
	public function __construct($sMessage = '', $iCode = 0, $oPrevious = null)
	{
		$sMessage = 0 === strlen($sMessage) ? str_replace('\\', '-', get_class($this))
//			.' ('. basename($this->getFile()).' ~ '.$this->getLine().')' 
				: $sMessage;

		parent::__construct($sMessage, $iCode, $oPrevious);
	}
}
