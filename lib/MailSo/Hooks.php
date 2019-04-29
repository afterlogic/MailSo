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
class Hooks
{
	/**
	 * @var array
	 */
	static $aCallbacks = array();

	/**
	 * @param string $sName
	 * @param array $aArg
	 */
	static public function Run($sName, $aArg = array())
	{
		if (isset(\MailSo\Hooks::$aCallbacks[$sName]))
		{
			foreach (\MailSo\Hooks::$aCallbacks[$sName] as $mCallback)
			{
				\call_user_func_array($mCallback, $aArg);
			}
		}
	}

	/**
	 * @param string $sName
	 * @param mixed $mCallback
	 */
	static public function Add($sName, $mCallback)
	{
		if (\is_callable($mCallback))
		{
			if (!isset(\MailSo\Hooks::$aCallbacks[$sName]))
			{
				\MailSo\Hooks::$aCallbacks[$sName] = array();
			}

			\MailSo\Hooks::$aCallbacks[$sName][] = $mCallback;
		}
	}
}
