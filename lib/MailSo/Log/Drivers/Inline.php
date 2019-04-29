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
class Inline extends \MailSo\Log\Driver
{
	/**
	 * @var string
	 */
	private $sNewLine;

	/**
	 * @var bool
	 */
	private $bHtmlEncodeSpecialChars;

	/**
	 * @access protected
	 *
	 * @param string $sNewLine = "\r\n"
	 * @param bool $bHtmlEncodeSpecialChars = false
	 */
	protected function __construct($sNewLine = "\r\n", $bHtmlEncodeSpecialChars = false)
	{
		parent::__construct();

		$this->sNewLine = $sNewLine;
		$this->bHtmlEncodeSpecialChars = $bHtmlEncodeSpecialChars;
	}

	/**
	 * @param string $sNewLine = "\r\n"
	 * @param bool $bHtmlEncodeSpecialChars = false
	 *
	 * @return \MailSo\Log\Drivers\Inline
	 */
	public static function NewInstance($sNewLine = "\r\n", $bHtmlEncodeSpecialChars = false)
	{
		return new self($sNewLine, $bHtmlEncodeSpecialChars);
	}

	/**
	 * @param string $mDesc
	 *
	 * @return bool
	 */
	protected function writeImplementation($mDesc)
	{
		if (\is_array($mDesc))
		{
			if ($this->bHtmlEncodeSpecialChars)
			{
				$mDesc = \array_map(function ($sItem) {
					return \htmlspecialchars($sItem);
				}, $mDesc);
			}

			$mDesc = \implode($this->sNewLine, $mDesc);
		}
		else
		{
			echo ($this->bHtmlEncodeSpecialChars) ? \htmlspecialchars($mDesc).$this->sNewLine : $mDesc.$this->sNewLine;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	protected function clearImplementation()
	{
		if (\defined('PHP_SAPI') && 'cli' === PHP_SAPI && \MailSo\Base\Utils::FunctionExistsAndEnabled('system'))
		{
			\system('clear');
		}

		return true;
	}
}
