<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Imap;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Imap
 */
class FolderInformation
{
	/**
	 * @var string
	 */
	public $FolderName;

	/**
	 * @var bool
	 */
	public $IsWritable;

	/**
	 * @var array
	 */
	public $Flags;

	/**
	 * @var array
	 */
	public $PermanentFlags;

	/**
	 * @var int
	 */
	public $Exists;

	/**
	 * @var int
	 */
	public $Recent;

	/**
	 * @var string
	 */
	public $Uidvalidity;

	/**
	 * @var int
	 */
	public $Unread;

	/**
	 * @var string
	 */
	public $Uidnext;

	/**
	 * @access private
	 * 
	 * @param string $sFolderName
	 * @param bool $bIsWritable
	 */
	private function __construct($sFolderName, $bIsWritable)
	{
		$this->FolderName = $sFolderName;
		$this->IsWritable = $bIsWritable;
		$this->Exists = null;
		$this->Recent = null;
		$this->Flags = array();
		$this->PermanentFlags = array();

		$this->Unread = null;
		$this->Uidnext = null;
	}

	/**
	 * @param string $sFolderName
	 * @param bool $bIsWritable
	 *
	 * @return \MailSo\Imap\FolderInformation
	 */
	public static function NewInstance($sFolderName, $bIsWritable)
	{
		return new self($sFolderName, $bIsWritable);
	}

	/**
	 * @param string $sFlag
	 *
	 * @return bool
	 */
	public function IsFlagSupported($sFlag)
	{
		return in_array('\\*', $this->PermanentFlags) || in_array($sFlag, $this->PermanentFlags);
	}
}
