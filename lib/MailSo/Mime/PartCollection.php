<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Mime;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Mime
 */
class PartCollection extends \MailSo\Base\Collection
{
	/**
	 * @access protected
	 */
	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return \MailSo\Mime\PartCollection
	 */
	public static function NewInstance()
	{
		return new self();
	}

	/**
	 * @param string $sBoundary
	 *
	 * @return resorce
	 */
	public function ToStream($sBoundary)
	{
		$rResult = null;
		if (0 < \strlen($sBoundary))
		{
			$aResult = array();

			$aParts =& $this->GetAsArray();
			foreach ($aParts as /* @var $oPart \MailSo\Mime\Part */ &$oPart)
			{
				if (0 < count($aResult))
				{
					$aResult[] = \MailSo\Mime\Enumerations\Constants::CRLF.
						'--'.$sBoundary.\MailSo\Mime\Enumerations\Constants::CRLF;
				}

				$aResult[] = $oPart->ToStream();
			}
			
			return \MailSo\Base\StreamWrappers\SubStreams::CreateStream($aResult);
		}

		return $rResult;
	}
}
