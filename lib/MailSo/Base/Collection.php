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
abstract class Collection
{
	/**
	 * @var array
	 */
	protected $aItems;

	/**
	 * @access protected
	 */
	protected function __construct()
	{
		$this->aItems = array();
	}

	/**
	 * @param mixed $mItem
	 * @param bool $bToTop = false
	 * @return self
	 */
	public function Add($mItem, $bToTop = false)
	{
		if ($bToTop)
		{
			\array_unshift($this->aItems, $mItem);
		}
		else
		{
			\array_push($this->aItems, $mItem);
		}

		return $this;
	}

	/**
	 * @param array $aItems
	 * @return self
	 *
	 * @throws \MailSo\Base\Exceptions\InvalidArgumentException
	 */
	public function AddArray($aItems)
	{
		if (!\is_array($aItems))
		{
			throw new \MailSo\Base\Exceptions\InvalidArgumentException();
		}

		foreach ($aItems as $mItem)
		{
			$this->Add($mItem);
		}

		return $this;
	}

	/**
	 * @return self
	 */
	public function clear()
	{
		$this->aItems = array();

		return $this;
	}

	/**
	 * @return array
	 */
	public function CloneAsArray()
	{
		return $this->aItems;
	}

	/**
	 * @return int
	 */
	public function Count()
	{
		return \count($this->aItems);
	}

	/**
	 * @return array
	 */
	public function &GetAsArray()
	{
		return $this->aItems;
	}

	/**
	 * @param mixed $mCallback
	 */
	public function MapList($mCallback)
	{
		$aResult = array();
		if (\is_callable($mCallback))
		{
			foreach ($this->aItems as $oItem)
			{
				$aResult[] = \call_user_func($mCallback, $oItem);
			}
		}

		return $aResult;
	}

	/**
	 * @param mixed $mCallback
	 * @return array
	 */
	public function FilterList($mCallback)
	{
		$aResult = array();
		if (\is_callable($mCallback))
		{
			foreach ($this->aItems as $oItem)
			{
				if (\call_user_func($mCallback, $oItem))
				{
					$aResult[] = $oItem;
				}
			}
		}

		return $aResult;
	}

	/**
	 * @param mixed $mCallback
	 * @return void
	 */
	public function ForeachList($mCallback)
	{
		if (\is_callable($mCallback))
		{
			foreach ($this->aItems as $oItem)
			{
				\call_user_func($mCallback, $oItem);
			}
		}
	}

	/**
	 * @return mixed | null
	 * @return mixed
	 */
	public function &GetByIndex($mIndex)
	{
		$mResult = null;
		if (\key_exists($mIndex, $this->aItems))
		{
			$mResult = $this->aItems[$mIndex];
		}

		return $mResult;
	}

	/**
	 * @param array $aItems
	 * @return self
	 *
	 * @throws \MailSo\Base\Exceptions\InvalidArgumentException
	 */
	public function SetAsArray($aItems)
	{
		if (!\is_array($aItems))
		{
			throw new \MailSo\Base\Exceptions\InvalidArgumentException();
		}

		$this->aItems = $aItems;

		return $this;
	}
}
