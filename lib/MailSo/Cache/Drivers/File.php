<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Cache\Drivers;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Cache
 * @subpackage Drivers
 */
class File implements \MailSo\Cache\DriverInterface
{
	/**
	 * @var string
	 */
	private $sCacheFolder;
	
	/**
	 * @var bool
	 */
	public $bRootDir = false;	

	/**
	 * @access private
	 *
	 * @param string $sCacheFolder
	 */
	private function __construct($sCacheFolder)
	{
		$this->sCacheFolder = $sCacheFolder;
		$this->sCacheFolder = rtrim(trim($this->sCacheFolder), '\\/').'/';
		if (!\is_dir($this->sCacheFolder))
		{
			@\mkdir($this->sCacheFolder, 0755);
		}
	}

	/**
	 * @param string $sCacheFolder
	 *
	 * @return \MailSo\Cache\Drivers\File
	 */
	public static function NewInstance($sCacheFolder)
	{
		return new self($sCacheFolder);
	}

	/**
	 * @param string $sKey
	 * @param string $sValue
	 *
	 * @return bool
	 */
	public function Set($sKey, $sValue)
	{
		return false !== \file_put_contents($sPath = $this->generateCachedFileName($sKey, true), $sValue);
	}

	/**
	 * @param string $sKey
	 *
	 * @return string
	 */
	public function get($sKey)
	{
		$sValue = '';
		$sPath = $this->generateCachedFileName($sKey);
		if (\file_exists($sPath))
		{
			$sValue = \file_get_contents($sPath);
		}

		return \is_string($sValue) ? $sValue : '';
	}

	/**
	 * @param string $sKey
	 *
	 * @return void
	 */
	public function Delete($sKey)
	{
		$sPath = $this->generateCachedFileName($sKey);
		if (\file_exists($sPath))
		{
			\unlink($sPath);
		}
	}

	/**
	 * @param int $iTimeToClearInHours = 24
	 * 
	 * @return bool
	 */
	public function gc($iTimeToClearInHours = 24)
	{
		if (0 < $iTimeToClearInHours)
		{
			\MailSo\Base\Utils::RecTimeDirRemove($this->sCacheFolder, 60 * 60 * $iTimeToClearInHours, \time());
			return true;
		}
		
		return false;
	}

	/**
	 * @param string $sKey
	 * @param bool $bMkDir = false
	 *
	 * @return string
	 */
	private function generateCachedFileName($sKey, $bMkDir = false)
	{
		$sFilePath = '';
		if (3 < \strlen($sKey))
		{
			$sKeyPath = \sha1($sKey);
			if (!$this->bRootDir)
			{
				$sKeyPath = \substr($sKeyPath, 0, 2).'/'.\substr($sKeyPath, 2, 2).'/'.$sKeyPath;
			}

			$sFilePath = $this->sCacheFolder.'/'.$sKeyPath;
			if ($bMkDir && !\is_dir(\dirname($sFilePath)))
			{
				if (!\mkdir(\dirname($sFilePath), 0755, true))
				{
					$sFilePath = '';
				}
			}
		}

		return $sFilePath;
	}
}
