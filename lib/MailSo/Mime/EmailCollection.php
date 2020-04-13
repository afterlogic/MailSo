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
class EmailCollection extends \MailSo\Base\Collection
{
	/**
	 * @access protected
	 *
	 * @param string $sEmailAddresses = ''
	 */
	protected function __construct($sEmailAddresses = '')
	{
		parent::__construct();

		if (0 < \strlen($sEmailAddresses))
		{
			$this->parseEmailAddresses($sEmailAddresses);
		}
	}

	/**
	 * @param string $sEmailAddresses = ''
	 *
	 * @return \MailSo\Mime\EmailCollection
	 */
	public static function NewInstance($sEmailAddresses = '')
	{
		return new self($sEmailAddresses);
	}

	/**
	 * @param string $sEmailAddresses
	 *
	 * @return \MailSo\Mime\EmailCollection
	 */
	public static function Parse($sEmailAddresses)
	{
		return self::NewInstance($sEmailAddresses);
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		$aReturn = $aEmails = array();
		$aEmails =& $this->GetAsArray();
		foreach ($aEmails as /* @var $oEmail \MailSo\Mime\Email */ $oEmail)
		{
			$aReturn[] = $oEmail->toArray();
		}

		return $aReturn;
	}

	/**
	 * @param \MailSo\Mime\EmailCollection $oEmails
	 *
	 * @return \MailSo\Mime\EmailCollection
	 */
	public function MergeWithOtherCollection(\MailSo\Mime\EmailCollection $oEmails)
	{
		$aEmails =& $oEmails->GetAsArray();
		foreach ($aEmails as /* @var $oEmail \MailSo\Mime\Email */ $oEmail)
		{
			$this->Add($oEmail);
		}

		return $this;
	}

	/**
	 * @return \MailSo\Mime\EmailCollection
	 */
	public function Unique()
	{
		$aCache = array();
		$aReturn = array();

		$aEmails =& $this->GetAsArray();
		foreach ($aEmails as /* @var $oEmail \MailSo\Mime\Email */ $oEmail)
		{
			$sEmail = $oEmail->GetEmail();
			if (!isset($aCache[$sEmail]))
			{
				$aCache[$sEmail] = true;
				$aReturn[] = $oEmail;
			}
		}

		$this->SetAsArray($aReturn);

		return $this;
	}

	/**
	 * @param bool $bConvertSpecialsName = false
	 * @param bool $bIdn = false
	 *
	 * @return string
	 */
	public function ToString($bConvertSpecialsName = false, $bIdn = false)
	{
		$aReturn = $aEmails = array();
		$aEmails =& $this->GetAsArray();
		foreach ($aEmails as /* @var $oEmail \MailSo\Mime\Email */ $oEmail)
		{
			$aReturn[] = $oEmail->ToString($bConvertSpecialsName, $bIdn);
		}

		return \implode(', ', $aReturn);
	}

	/**
	 * @param string $sRawEmails
	 *
	 * @return \MailSo\Mime\EmailCollection
	 */
	private function parseEmailAddresses($sRawEmails)
	{
		$this->clear();

		$sWorkingRecipients = \trim($sRawEmails);

		if (0 === \strlen($sWorkingRecipients))
		{
			return $this;
		}

		$iEmailStartPos = 0;
		$iEmailEndPos = 0;

		$bIsInQuotes = false;
		$sChQuote = '"';
		$bIsInAngleBrackets = false;
		$bIsInBrackets = false;

		$iCurrentPos = 0;

		$sWorkingRecipientsLen = \strlen($sWorkingRecipients);

		while ($iCurrentPos < $sWorkingRecipientsLen)
		{
			switch ($sWorkingRecipients[$iCurrentPos])
			{
				case '\'':
				case '"':
					if (!$bIsInQuotes)
					{
						$sChQuote = $sWorkingRecipients[$iCurrentPos];
						$bIsInQuotes = true;
					}
					else if ($sChQuote == $sWorkingRecipients[$iCurrentPos])
					{
						$bIsInQuotes = false;
					}
					break;

				case '<':
					if (!$bIsInAngleBrackets)
					{
						$bIsInAngleBrackets = true;
						if ($bIsInQuotes)
						{
							$bIsInQuotes = false;
						}
					}
					break;

				case '>':
					if ($bIsInAngleBrackets)
					{
						$bIsInAngleBrackets = false;
					}
					break;

				case '(':
					if (!$bIsInBrackets)
					{
						$bIsInBrackets = true;
					}
					break;

				case ')':
					if ($bIsInBrackets)
					{
						$bIsInBrackets = false;
					}
					break;

				case ',':
				case ';':
					if (!$bIsInAngleBrackets && !$bIsInBrackets && !$bIsInQuotes)
					{
						$iEmailEndPos = $iCurrentPos;

						try
						{
							$this->Add(
								\MailSo\Mime\Email::Parse(\substr($sWorkingRecipients, $iEmailStartPos, $iEmailEndPos - $iEmailStartPos))
							);

							$iEmailStartPos = $iCurrentPos + 1;
						}
						catch (\MailSo\Base\Exceptions\InvalidArgumentException $oException)
						{
						}
					}
					break;
			}

			$iCurrentPos++;
		}

		if ($iEmailStartPos < $iCurrentPos)
		{
			try
			{
				$this->Add(
					\MailSo\Mime\Email::Parse(\substr($sWorkingRecipients, $iEmailStartPos, $iCurrentPos - $iEmailStartPos))
				);
			}
			catch (\MailSo\Base\Exceptions\InvalidArgumentException $oException) {}
		}

		return $this;
	}
}
