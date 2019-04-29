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
class ResourceRegistry
{
	/**
	 * @var array
	 */
	public static $Resources = array();

	/**
	 * @access private
	 */
	private function __construct()
	{
	}

	/**
	 * @staticvar bool $bInited
	 *
	 * @return void
	 */
	private static function regResourcesShutdownFunc()
	{
		static $bInited = false;
		if (!$bInited)
		{
			$bInited = true;
			\register_shutdown_function(function () {
				if (\is_array(\MailSo\Base\ResourceRegistry::$Resources))
				{
					foreach (\array_keys(\MailSo\Base\ResourceRegistry::$Resources) as $sKey)
					{
						if (\is_resource(\MailSo\Base\ResourceRegistry::$Resources[$sKey]))
						{
							\MailSo\Base\Loader::IncStatistic('CloseMemoryResource');
							\fclose(\MailSo\Base\ResourceRegistry::$Resources[$sKey]);
						}
						\MailSo\Base\ResourceRegistry::$Resources[$sKey] = null;
					}
				}

				\MailSo\Base\ResourceRegistry::$Resources = array();
			});
		}
	}

	/**
	 * @param int $iMemoryMaxInMb = 5
	 *
	 * @return resource | bool
	 */
	public static function CreateMemoryResource($iMemoryMaxInMb = 5)
	{
		self::regResourcesShutdownFunc();

		$oResult = @\fopen('php://temp/maxmemory:'.($iMemoryMaxInMb * 1024 * 1024), 'r+b');
		if (\is_resource($oResult))
		{
			\MailSo\Base\Loader::IncStatistic('CreateMemoryResource');
			\MailSo\Base\ResourceRegistry::$Resources[(string) $oResult] = $oResult;
			return $oResult;
		}

		return false;
	}

	/**
	 * @param string $sString
	 *
	 * @return resource | bool
	 */
	public static function CreateMemoryResourceFromString($sString)
	{
		$oResult = self::CreateMemoryResource();
		if (\is_resource($oResult))
		{
			\fwrite($oResult, $sString);
			\rewind($oResult);
		}

		return $oResult;
	}

	/**
	 * @param resource $rResource
	 *
	 * @return void
	 */
	public static function CloseMemoryResource(&$rResource)
	{
		if (\is_resource($rResource))
		{
			$sKey = (string) $rResource;
			if (isset(\MailSo\Base\ResourceRegistry::$Resources[$sKey]))
			{
				\fclose(\MailSo\Base\ResourceRegistry::$Resources[$sKey]);
				\MailSo\Base\ResourceRegistry::$Resources[$sKey] = null;
				unset(\MailSo\Base\ResourceRegistry::$Resources[$sKey]);
				\MailSo\Base\Loader::IncStatistic('CloseMemoryResource');
			}

			if (\is_resource($rResource))
			{
				\fclose($rResource);
			}

			$rResource = null;
		}
	}
}
