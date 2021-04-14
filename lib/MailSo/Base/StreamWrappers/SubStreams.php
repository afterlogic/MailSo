<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base\StreamWrappers;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Base
 * @subpackage StreamWrappers
 */
class SubStreams
{
	/**
	 * @var string
	 */
	const STREAM_NAME = 'mailsosubstreams';

	/**
	 * @var array
	 */
	private static $aStreams = array();

	/**
	 * @var array
	 */
	private $aSubStreams;

	/**
	 * @var int
	 */
	private $iIndex;

	/**
	 * @var string
	 */
	private $sBuffer;

	/**
	 * @var bool
	 */
	private $bIsEnd;

	/**
	 * @var int
	 */
	private $iPos;

	/**
	 * @var string
	 */
	private $sHash;

	/**
	 * @param array $aSubStreams
	 *
	 * @return resource|bool
	 */
	public static function CreateStream($aSubStreams)
	{
		if (!\in_array(self::STREAM_NAME, \stream_get_wrappers()))
		{
			\stream_wrapper_register(self::STREAM_NAME, '\MailSo\Base\StreamWrappers\SubStreams');
		}

		$sHashName = \md5(\microtime(true).\rand(1000, 9999));

		self::$aStreams[$sHashName] = $aSubStreams;

		\MailSo\Base\Loader::IncStatistic('CreateStream/SubStreams');

		return \fopen(self::STREAM_NAME.'://'.$sHashName, 'rb');
	}

	/**
	 * @return resource|null
	 */
	protected function &getPart()
	{
		$nNull = null;
		if (isset($this->aSubStreams[$this->iIndex]))
		{
			return $this->aSubStreams[$this->iIndex];
		}

		return $nNull;
	}

	/**
	 * @param string $sPath
	 *
	 * @return bool
	 */
	public function stream_open($sPath)
	{
		$this->aSubStreams = array();

		$bResult = false;
		$aPath = \parse_url($sPath);

		if (isset($aPath['host'], $aPath['scheme']) &&
			0 < \strlen($aPath['host']) && 0 < \strlen($aPath['scheme']) &&
			self::STREAM_NAME === $aPath['scheme'])
		{
			$sHashName = $aPath['host'];
			$this->sHash = $sHashName;

			if (isset(self::$aStreams[$sHashName]) &&
				\is_array(self::$aStreams[$sHashName]) &&
				0 < \count(self::$aStreams[$sHashName]))
			{
				$this->iIndex = 0;
				$this->iPos = 0;
				$this->bIsEnd = false;
				$this->sBuffer = '';
				$this->aSubStreams = self::$aStreams[$sHashName];
			}

			$bResult = 0 < \count($this->aSubStreams);
		}

		return $bResult;
	}

	/**
	 * @param int $iCount
	 *
	 * @return string
	 */
	public function stream_read($iCount)
	{
		$sReturn = '';
		$mCurrentPart = null;
		
		if ($iCount > 0)
		{
			if ($iCount < \strlen($this->sBuffer))
			{
				$sReturn = \substr($this->sBuffer, 0, $iCount);
				$this->sBuffer = \substr($this->sBuffer, $iCount);
			}
			else
			{
				$sReturn = $this->sBuffer;
				while ($iCount > 0)
				{
					$mCurrentPart =& $this->getPart();
					if (null === $mCurrentPart)
					{
						$this->bIsEnd = true;
						$this->sBuffer = '';
						$iCount = 0;
						break;
					}

					if (\is_resource($mCurrentPart))
					{
						if(!\feof($mCurrentPart))
						{
							$sReadResult = @\fread($mCurrentPart, $iCount);

							if (false === $sReadResult)
							{
								return false;
							}
							
							$sReturn .= $sReadResult;
						}
						else
						{
							$this->iIndex++;
						}
					}
					else if (\is_string($mCurrentPart))
					{
						$sReturn .= $mCurrentPart;
						$this->iIndex++;
					}
					
					$iLen = \strlen($sReturn);
					if ($iCount < $iLen)
					{
						$this->sBuffer = \substr($sReturn, $iCount);
						$sReturn = \substr($sReturn, 0, $iCount);
						$iCount = 0;
					}
					else
					{
						$iCount -= $iLen;
					}
				}
			}

			$this->iPos += \strlen($sReturn);
			return $sReturn;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function stream_write()
	{
		return 0;
	}

	/**
	 * @return int
	 */
	public function stream_tell()
	{
		return $this->iPos;
	}

	/**
	 * @return bool
	 */
	public function stream_eof()
	{
		return $this->bIsEnd;
	}

	/**
	 * @return array
	 */
	public function stream_stat()
	{
		return array(
			'dev' => 2,
			'ino' => 0,
			'mode' => 33206,
			'nlink' => 1,
			'uid' => 0,
			'gid' => 0,
			'rdev' => 2,
			'size' => 0,
			'atime' => 1061067181,
			'mtime' => 1056136526,
			'ctime' => 1056136526,
			'blksize' => -1,
			'blocks' => -1
		);
	}

	/**
	 * @return bool
	 */
	public function stream_seek()
	{
		return false;
	}

	static public function setGlobalCounter($value)
    {
		$GLOBALS['counter'] = $value;
    }

    static public function getGlobalCounter($defValue = 0)
    {
        if (!isset($GLOBALS['counter']))
		{
			self::setGlobalCounter($defValue);
		}
		return $GLOBALS['counter'];
    }

	static public function incGlobalCounter()
    {
		self::setGlobalCounter(self::getGlobalCounter() + 1);
    }

}
