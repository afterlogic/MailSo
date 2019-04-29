<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Log\Drivers;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Log
 * @subpackage Drivers
 */
class Callback extends \MailSo\Log\Driver
{
	/**
	 * @var mixed
	 */
	private $fWriteCallback;

	/**
	 * @var mixed
	 */
	private $fClearCallback;

	/**
	 * @access protected
	 *
	 * @param mixed $fWriteCallback
	 * @param mixed $fClearCallback
	 */
	protected function __construct($fWriteCallback, $fClearCallback)
	{
		parent::__construct();

		$this->fWriteCallback = \is_callable($fWriteCallback) ? $fWriteCallback : null;
		$this->fClearCallback = \is_callable($fClearCallback) ? $fClearCallback : null;
	}

	/**
	 * @param mixed $fWriteCallback
	 * @param mixed $fClearCallback = null
	 *
	 * @return \MailSo\Log\Drivers\Callback
	 */
	public static function NewInstance($fWriteCallback, $fClearCallback = null)
	{
		return new self($fWriteCallback, $fClearCallback);
	}

	/**
	 * @param string|array $mDesc
	 *
	 * @return bool
	 */
	protected function writeImplementation($mDesc)
	{
		if ($this->fWriteCallback)
		{
			\call_user_func_array($this->fWriteCallback, array($mDesc));
		}

		return true;
	}
	
	/**
	 * @return bool
	 */
	protected function clearImplementation()
	{
		if ($this->fClearCallback)
		{
			\call_user_func($this->fClearCallback);
		}

		return true;
	}
}
