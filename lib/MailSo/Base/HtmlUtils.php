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
class HtmlUtils
{
	static $KOS = '@@_KOS_@@';
	static $aContentLocationUrls = [];
	static $fAdditionalExternalFilter = null;
	static $bDoNotReplaceExternalUrl = false;
	static $aFoundCIDs = [];
	static $bHasExternals = false;
	static $aFoundedContentLocationUrls = [];
	static $aNodesToRemove = [];
	static $maxNestingLevel = 0;
	static $bCreateHtmlLinksFromTextLinksInDOM = false;
	static $oDom = null;

	/**
	 * @access private
	 */
	private function __construct()
	{
	}

	/**
	 * @param string $sText
	 * @param string $sHtmlAttrs = ''
	 * @param string $sBodyAttrs = ''
	 *
	 * @return \DOMDocument|bool
	 */
	public static function GetDomFromText($sText, $sHtmlAttrs = '', $sBodyAttrs = '')
	{
		static $bOnce = true;
		if ($bOnce)
		{
			$bOnce = false;
			if (\MailSo\Base\Utils::FunctionExistsAndEnabled('libxml_use_internal_errors'))
			{
				@\libxml_use_internal_errors(true);
			}
		}

		$oDom = new \DOMDocument('1.0', 'utf-8');
		$oDom->encoding = 'UTF-8';
		$oDom->formatOutput = false;

		@$oDom->loadHTML('<'.'?xml version="1.0" encoding="utf-8"?'.'>'.
			'<html '.$sHtmlAttrs.'><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body '.$sBodyAttrs.'>'.$sText.'</body></html>');

		// Uncomment the following lines to get parsing errors in the log file.
		// Add LIBXML_PARSEHUGE as second parametr to loadHTML method if you get "Excessive depth in document: 256 use XML_PARSE_HUGE option" error
//		if (\MailSo\Base\Utils::FunctionExistsAndEnabled('libxml_get_errors'))
//		{
//			\Aurora\System\Api::Log(libxml_get_errors());
//		}

		return $oDom;
	}

	/**
	 * @param string $sHtml
	 * @param string $sHtmlAttrs = '
	 * @param string $sBodyAttrs = ''
	 *
	 * @return string
	 */
	public static function ClearBodyAndHtmlTag($sHtml, &$sHtmlAttrs = '', &$sBodyAttrs = '')
	{
		$aMatch = array();
		if (preg_match('/<html([^>]+)>/im', $sHtml, $aMatch) && !empty($aMatch[1]))
		{
			$sHtmlAttrs = $aMatch[1];
		}

		$aMatch = array();
		if (preg_match('/<body([^>]+)>/im', $sHtml, $aMatch) && !empty($aMatch[1]))
		{
			$sBodyAttrs = $aMatch[1];
		}

		$sHtml = \preg_replace('/\s*<body([^>]*)>/im', '', $sHtml);
		$sHtml = \preg_replace('/<\/body>\s*/im', '', $sHtml);
		$sHtml = \preg_replace('/\s*<html([^>]*)>/im', '', $sHtml);
		$sHtml = \preg_replace('/<\/html>\s*/im', '', $sHtml);

		return $sHtml;
	}

	/**
	 * @param string $sHtml
	 *
	 * @return string
	 */
	public static function ClearTags($sHtml)
	{
		$aRemoveTags = array(
			'head', 'link', 'base', /*'meta',*/ 'title', 'style', 'script', 'bgsound', 'keygen', 'source',
			'object', 'embed', 'applet', 'mocha', 'iframe', 'frame', 'frameset', 'video', 'audio'
		);

		$aToRemove = array(
			'/<!doctype[^>]*>/msi',
			'/<\?xml [^>]*\?>/msi'
		);

		foreach ($aRemoveTags as $sTag)
		{
			$aToRemove[] = '\'<'.$sTag.'[^>]*>.*?</[\s]*'.$sTag.'>\'msi';
			$aToRemove[] = '\'<'.$sTag.'[^>]*>\'msi';
			$aToRemove[] = '\'</[\s]*'.$sTag.'[^>]*>\'msi';
		}

		return \preg_replace($aToRemove, '', $sHtml);
	}

	/**
	 * @param string $sHtml
	 *
	 * @return string
	 */
	public static function ClearOn($sHtml)
	{
		$aToReplace = array(
			'/on(Blur)/si',
			'/on(Change)/si',
			'/on(Click)/si',
			'/on(DblClick)/si',
			'/on(Error)/si',
			'/on(Focus)/si',
			'/on(FormChange)/si',
			'/on(KeyDown)/si',
			'/on(KeyPress)/si',
			'/on(KeyUp)/si',
			'/on(Load)/si',
			'/on(MouseDown)/si',
			'/on(MouseEnter)/si',
			'/on(MouseLeave)/si',
			'/on(MouseMove)/si',
			'/on(MouseOut)/si',
			'/on(MouseOver)/si',
			'/on(MouseUp)/si',
			'/on(Move)/si',
			'/on(Resize)/si',
			'/on(ResizeEnd)/si',
			'/on(ResizeStart)/si',
			'/on(Scroll)/si',
			'/on(Select)/si',
			'/on(Submit)/si',
			'/on(Unload)/si'
		);

		return \preg_replace($aToReplace, 'оn\\1', $sHtml);
	}

	/**
	 *
	 * @param string $sStyle
	 * @param \DOMElement $oElement
	 * @param bool $bHasExternals
	 * @param array $aFoundCIDs
	 * @param array $aContentLocationUrls
	 * @param array $aFoundedContentLocationUrls
	 * @param bool $bDoNotReplaceExternalUrl = false
	 * @param callback|null $fAdditionalExternalFilter = null
	 *
	 * @return string
	 */
	public static function ClearStyle($sStyle, $oElement, &$bHasExternals, &$aFoundCIDs,
		$aContentLocationUrls, &$aFoundedContentLocationUrls, $bDoNotReplaceExternalUrl = false, $fAdditionalExternalFilter = null)
	{
		$sStyle = \trim($sStyle);
		$aOutStyles = array();
		$aStyles = \explode(';', $sStyle);

		if ($fAdditionalExternalFilter && !\is_callable($fAdditionalExternalFilter))
		{
			$fAdditionalExternalFilter = null;
		}

		$aMatch = array();
		foreach ($aStyles as $sStyleItem)
		{
			$aStyleValue = \explode(':', $sStyleItem, 2);
			$sName = \trim(\strtolower($aStyleValue[0]));
			$sValue = isset($aStyleValue[1]) ? \trim($aStyleValue[1]) : '';

			if ('position' === $sName && 'fixed' === \strtolower($sValue))
			{
				$sValue = 'absolute';
			}

			if (0 === \strlen($sName) || 0 === \strlen($sValue))
			{
				continue;
			}

			$sStyleItem = $sName.': '.$sValue;
			$aStyleValue = array($sName, $sValue);

			/*if (\in_array($sName, array('position', 'left', 'right', 'top', 'bottom', 'behavior', 'cursor')))
			{
				// skip
			}
			else */if (\in_array($sName, array('behavior')) ||
				('cursor' === $sName && !\in_array(\strtolower($sValue), array('none', 'cursor'))) ||
				('display' === $sName && 'none' === \strtolower($sValue)) ||
				\preg_match('/expression/i', $sValue) ||
				('text-indent' === $sName && '-' === \substr(trim($sValue), 0, 1))
			)
			{
				// skip
			}
			else if (\in_array($sName, array('background-image', 'background', 'list-style-image', 'content', 'shape-outside', 'border-image', 'border-image-source', 'list-style', '-webkit-mask-image'))
				&& \preg_match('/url[\s]?\(([^)]+)\)/im', $sValue, $aMatch) && !empty($aMatch[1]))
			{
				$sFullUrl = \trim($aMatch[0], '"\' ');
				$sUrl = \trim($aMatch[1], '"\' ');
				$sStyleValue = \trim(\preg_replace('/[\s]+/', ' ', \str_replace($sFullUrl, '', $sValue)));
				$sStyleItem = empty($sStyleValue) ? '' : $sName.': '.$sStyleValue;

				if ('cid:' === \strtolower(\substr($sUrl, 0, 4)))
				{
					if ($oElement)
					{
						$oElement->setAttribute('data-x-style-cid-name',
							'background' === $sName ? 'background-image' : $sName);

						$oElement->setAttribute('data-x-style-cid', \substr($sUrl, 4));

						$aFoundCIDs[] = \substr($sUrl, 4);
					}
				}
				else
				{
					if ($oElement)
					{
						if (\preg_match('/http[s]?:\/\//i', $sUrl))
						{
							$bHasExternals = true;
							if (!$bDoNotReplaceExternalUrl)
							{
								if (\in_array($sName, array('background-image', 'list-style-image', 'content')))
								{
									$sStyleItem = '';
								}

								$sTemp = '';
								if ($oElement->hasAttribute('data-x-style-url'))
								{
									$sTemp = \trim($oElement->getAttribute('data-x-style-url'));
								}

								$sTemp = empty($sTemp) ? '' : (';' === \substr($sTemp, -1) ? $sTemp.' ' : $sTemp.'; ');

								$oElement->setAttribute('data-x-style-url', \trim($sTemp.
									('background' === $sName ? 'background-image' : $sName).': '.$sFullUrl, ' ;'));

								if ($fAdditionalExternalFilter)
								{
									$sAdditionalResult = \call_user_func($fAdditionalExternalFilter, $sUrl);
									if (0 < \strlen($sAdditionalResult))
									{
										$oElement->setAttribute('data-x-additional-style-url',
											('background' === $sName ? 'background-image' : $sName).': url('.$sAdditionalResult.')');
									}
								}
							}
						}
						else if ('data:image/' !== \strtolower(\substr(\trim($sUrl), 0, 11)))
						{
							$oElement->setAttribute('data-x-broken-style-src', $sFullUrl);
						}
					}
				}

				if (!empty($sStyleItem))
				{
					$aOutStyles[] = $sStyleItem;
				}
			}
			else if ('height' === $sName)
			{
//				$aOutStyles[] = 'min-'.ltrim($sStyleItem);
				$aOutStyles[] = $sStyleItem;
			}
			else
			{
				$aOutStyles[] = $sStyleItem;
			}
		}

		return \implode(';', $aOutStyles);
	}

	/**
	 * Replaces text like address@domain with <a href="mailto:address@domain">address@domain</a> in the message body.
	 * This method performs this replacement for HTML messages. In plain-text messages, links are always converted to <a>.
	 * @param \DOMElement $oElement
	 */
	public static function CreateHtmlLinksFromTextLinksInDOMElement($oElement)
	{
		$sTagNameLower = \strtolower($oElement->tagName);
		$sParentTagNameLower = isset($oElement->parentNode) && isset($oElement->parentNode->tagName) ?
			\strtolower($oElement->parentNode->tagName) : '';

		if (!\in_array($sTagNameLower, array('html', 'meta', 'head', 'style', 'script', 'img', 'button', 'input', 'textarea', 'a')) &&
			'a' !== $sParentTagNameLower && $oElement->childNodes && 0 < $oElement->childNodes->length)
		{
			$oSubItem = null;
			$aTextNodes = array();
			$iIndex = $oElement->childNodes->length - 1;
			while ($iIndex > -1)
			{
				$oSubItem = $oElement->childNodes->item($iIndex);
				if ($oSubItem && XML_TEXT_NODE === $oSubItem->nodeType)
				{
					$aTextNodes[] = $oSubItem;
				}

				$iIndex--;
			}

			unset($oSubItem);

			foreach ($aTextNodes as $oTextNode)
			{
				if ($oTextNode && 0 < \strlen($oTextNode->wholeText)/* && \preg_match('/http[s]?:\/\//i', $oTextNode->wholeText)*/)
				{
					$sText = \MailSo\Base\LinkFinder::NewInstance()
						->Text($oTextNode->wholeText)
						->UseDefaultWrappers(true)
						->CompileText()
					;

					$oSubDom = \MailSo\Base\HtmlUtils::GetDomFromText('<html><body>'.$sText.'</body></html>');
					if ($oSubDom)
					{
						$oBodyNodes = $oSubDom->getElementsByTagName('body');
						if ($oBodyNodes && 0 < $oBodyNodes->length)
						{
							$oBodyChildNodes = $oBodyNodes->item(0)->childNodes;
							if ($oBodyChildNodes && $oBodyChildNodes->length)
							{
								for ($iIndex = 0, $iLen = $oBodyChildNodes->length; $iIndex < $iLen; $iIndex++)
								{
									$oSubItem = $oBodyChildNodes->item($iIndex);
									if ($oSubItem)
									{
										if (XML_ELEMENT_NODE === $oSubItem->nodeType &&
											'a' === \strtolower($oSubItem->tagName))
										{
											$oLink = self::$oDom->createElement('a',
												\str_replace(':', \MailSo\Base\HtmlUtils::$KOS, \htmlspecialchars($oSubItem->nodeValue)));

											$sHref = $oSubItem->getAttribute('href');
											if ($sHref)
											{
												$oLink->setAttribute('href', $sHref);
											}

											$oElement->insertBefore($oLink, $oTextNode);
										}
										else
										{
											$oElement->insertBefore(self::$oDom->importNode($oSubItem), $oTextNode);
										}
									}
								}

								$oElement->removeChild($oTextNode);
							}
						}

						unset($oBodyNodes);
					}

					unset($oSubDom, $sText);
				}
			}
		}
	}

	/**
	 * @param string $sHtml
	 * @param bool $bDoNotReplaceExternalUrl = false
	 * @param bool $bCreateHtmlLinksFromTextLinksInDOM = false
	 *
	 * @return string
	 */
	public static function ClearHtmlSimple($sHtml, $bDoNotReplaceExternalUrl = false, $bCreateHtmlLinksFromTextLinksInDOM = false)
	{
		$bHasExternals = false;
		$aFoundCIDs = array();
		$aContentLocationUrls = array();
		$aFoundedContentLocationUrls = array();

		return \MailSo\Base\HtmlUtils::ClearHtml($sHtml, $bHasExternals, $aFoundCIDs,
			$aContentLocationUrls, $aFoundedContentLocationUrls, $bDoNotReplaceExternalUrl, $bCreateHtmlLinksFromTextLinksInDOM);
	}

	/**
	 * @param string $sHtml
	 * @param bool $bHasExternals = false
	 * @param array $aFoundCIDs = array()
	 * @param array $aContentLocationUrls = array()
	 * @param array $aFoundedContentLocationUrls = array()
	 * @param bool $bDoNotReplaceExternalUrl = false
	 * @param bool $bCreateHtmlLinksFromTextLinksInDOM = false
	 * @param callback|null $fAdditionalExternalFilter = null
	 *
	 * @return string
	 */
	public static function ClearHtml($sHtml, &$bHasExternals = false, &$aFoundCIDs = array(),
		$aContentLocationUrls = array(), &$aFoundedContentLocationUrls = array(),
		$bDoNotReplaceExternalUrl = false, $bCreateHtmlLinksFromTextLinksInDOM = false, $fAdditionalExternalFilter = null)
	{
		self::$aContentLocationUrls = $aContentLocationUrls;
		self::$fAdditionalExternalFilter = $fAdditionalExternalFilter;
		self::$bDoNotReplaceExternalUrl = $bDoNotReplaceExternalUrl;
		self::$bHasExternals = $bHasExternals;
		self::$aFoundCIDs = $aFoundCIDs;
		self::$aFoundedContentLocationUrls  = $aFoundedContentLocationUrls;
		self::$bCreateHtmlLinksFromTextLinksInDOM = $bCreateHtmlLinksFromTextLinksInDOM;
		$sResult = '';
		$sHtml = null === $sHtml ? '' : (string) $sHtml;
		$sHtml = \trim($sHtml);
		if (0 === \strlen($sHtml))
		{
			return '';
		}

		if ($fAdditionalExternalFilter && !\is_callable($fAdditionalExternalFilter))
		{
			self::$fAdditionalExternalFilter = null;
		}

		self::$bHasExternals = false;

		$sHtml = \MailSo\Base\HtmlUtils::ClearTags($sHtml);
		$sHtml = \MailSo\Base\HtmlUtils::ClearOn($sHtml);

		$sHtmlAttrs = $sBodyAttrs = '';
		$sHtml = \MailSo\Base\HtmlUtils::ClearBodyAndHtmlTag($sHtml, $sHtmlAttrs, $sBodyAttrs);

		// Dom Part
		$oDom = \MailSo\Base\HtmlUtils::GetDomFromText($sHtml, $sHtmlAttrs, $sBodyAttrs);
		unset($sHtml);

		if ($oDom)
		{
			self::$oDom = $oDom;
			self::$maxNestingLevel = (int) @ini_get('xdebug.max_nesting_level');
			self::processNode($oDom, 0);
			foreach (self::$aNodesToRemove as $oElement)
			{
				if (isset($oElement->parentNode))
				{
					@$oElement->parentNode->removeChild($oElement);
				}
			}

			$sResult = $oDom->saveHTML();
		}

		unset($oDom);

		$sResult = \MailSo\Base\HtmlUtils::ClearTags($sResult);

		$sHtmlAttrs = $sBodyAttrs = '';
		$sResult = \MailSo\Base\HtmlUtils::ClearBodyAndHtmlTag($sResult, $sHtmlAttrs, $sBodyAttrs);
		$sResult = '<div data-x-div-type="body" '.$sBodyAttrs.'>'.$sResult.'</div>';
		$sResult = '<div data-x-div-type="html" '.$sHtmlAttrs.'>'.$sResult.'</div>';

		$sResult = \str_replace(\MailSo\Base\HtmlUtils::$KOS, ':', $sResult);
		$aFoundCIDs = self::$aFoundCIDs;
		$bHasExternals = self::$bHasExternals;
		$aFoundedContentLocationUrls = self::$aFoundedContentLocationUrls;

		return \trim($sResult);
	}

	/**
	 * @param string $sHtml
	 * @param array $aFoundCids = array()
	 * @param array|null $mFoundDataURL = null
	 * @param array $aFoundedContentLocationUrls = array()
	 *
	 * @return string
	 */
	public static function BuildHtml($sHtml, &$aFoundCids = array(), &$mFoundDataURL = null, &$aFoundedContentLocationUrls = array())
	{
		$oDom = \MailSo\Base\HtmlUtils::GetDomFromText($sHtml);
		unset($sHtml);

		$aNodes = $oDom->getElementsByTagName('*');
		foreach ($aNodes as /* @var $oElement \DOMElement */ $oElement)
		{
			$sTagNameLower = \strtolower($oElement->tagName);

			if ($oElement->hasAttribute('data-x-src-cid'))
			{
				$sCid = $oElement->getAttribute('data-x-src-cid');
				$oElement->removeAttribute('data-x-src-cid');

				if (!empty($sCid))
				{
					$aFoundCids[] = $sCid;

					@$oElement->removeAttribute('src');
					$oElement->setAttribute('src', 'cid:'.$sCid);
				}
			}

			if ($oElement->hasAttribute('data-x-src-location'))
			{
				$sSrc = $oElement->getAttribute('data-x-src-location');
				$oElement->removeAttribute('data-x-src-location');

				if (!empty($sSrc))
				{
					$aFoundedContentLocationUrls[] = $sSrc;

					@$oElement->removeAttribute('src');
					$oElement->setAttribute('src', $sSrc);
				}
			}

			if ($oElement->hasAttribute('data-x-broken-src'))
			{
				$oElement->setAttribute('src', $oElement->getAttribute('data-x-broken-src'));
				$oElement->removeAttribute('data-x-broken-src');
			}

			if ($oElement->hasAttribute('data-x-src'))
			{
				$oElement->setAttribute('src', $oElement->getAttribute('data-x-src'));
				$oElement->removeAttribute('data-x-src');
			}

			if ($oElement->hasAttribute('data-x-href'))
			{
				$oElement->setAttribute('href', $oElement->getAttribute('data-x-href'));
				$oElement->removeAttribute('data-x-href');
			}

			if ($oElement->hasAttribute('data-x-additional-src'))
			{
				$oElement->removeAttribute('data-x-additional-src');
			}

			if ($oElement->hasAttribute('data-x-additional-style-url'))
			{
				$oElement->removeAttribute('data-x-additional-style-url');
			}

			if ($oElement->hasAttribute('data-x-style-cid-name') && $oElement->hasAttribute('data-x-style-cid'))
			{
				$sCidName = $oElement->getAttribute('data-x-style-cid-name');
				$sCid = $oElement->getAttribute('data-x-style-cid');

				$oElement->removeAttribute('data-x-style-cid-name');
				$oElement->removeAttribute('data-x-style-cid');
				if (!empty($sCidName) && !empty($sCid) && \in_array($sCidName,
					array('background-image', 'background', 'list-style-image', 'content')))
				{
					$sStyles = '';
					if ($oElement->hasAttribute('style'))
					{
						$sStyles = \trim(\trim($oElement->getAttribute('style')), ';');
					}

					$sBack = $sCidName.': url(cid:'.$sCid.')';
					$sStyles = \preg_replace('/'.\preg_quote($sCidName, '/').':\s?[^;]+/i', $sBack, $sStyles);
					if (false === \strpos($sStyles, $sBack))
					{
						$sStyles .= empty($sStyles) ? '': '; ';
						$sStyles .= $sBack;
					}

					$oElement->setAttribute('style', $sStyles);
					$aFoundCids[] = $sCid;
				}
			}

			if ($oElement->hasAttribute('data-original'))
			{
				$oElement->removeAttribute('data-original');
			}

			if ($oElement->hasAttribute('data-x-div-type'))
			{
				$oElement->removeAttribute('data-x-div-type');
			}

			if ($oElement->hasAttribute('data-x-style-url'))
			{
				$sAddStyles = $oElement->getAttribute('data-x-style-url');
				$oElement->removeAttribute('data-x-style-url');

				if (!empty($sAddStyles))
				{
					$sStyles = '';
					if ($oElement->hasAttribute('style'))
					{
						$sStyles = \trim(\trim($oElement->getAttribute('style')), ';');
					}

					$oElement->setAttribute('style', (empty($sStyles) ? '' : $sStyles.'; ').$sAddStyles);
				}
			}

			if ('img' === $sTagNameLower && \is_array($mFoundDataURL))
			{
				$sSrc = $oElement->getAttribute('src');
				if ('data:image/' === \strtolower(\substr($sSrc, 0, 11)))
				{
					$sHash = \md5($sSrc);
					$mFoundDataURL[$sHash] = $sSrc;

					$oElement->setAttribute('src', 'cid:'.$sHash);
				}
			}
		}

		$sResult = $oDom->saveHTML();
		unset($oDom);

		$sResult = \MailSo\Base\HtmlUtils::ClearTags($sResult);
		$sResult = \MailSo\Base\HtmlUtils::ClearBodyAndHtmlTag($sResult);

		return '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>'.
			'<body>'.\trim($sResult).'</body></html>';
	}

	/**
	 * @param string $sText
	 * @param bool $bLinksWithTargetBlank = true
	 *
	 * @return string
	 */
	public static function ConvertPlainToHtml($sText, $bLinksWithTargetBlank = true)
	{
		$sText = \trim($sText);
		if (0 === \strlen($sText))
		{
			return '';
		}

		$sText = \MailSo\Base\LinkFinder::NewInstance()
			->Text($sText)
			->UseDefaultWrappers($bLinksWithTargetBlank)
			->CompileText();

		$sText = \str_replace("\r", '', $sText);

		$aText = \explode("\n", $sText);
		unset($sText);

		$bIn = false;
		$bDo = true;
		do
		{
			$bDo = false;
			$aNextText = array();
			foreach ($aText as $sTextLine)
			{
				$bStart = 0 === \strpos(\ltrim($sTextLine), '&gt;');
				if ($bStart && !$bIn)
				{
					$bDo = true;
					$bIn = true;
					$aNextText[] = '<blockquote>';
					$aNextText[] = \substr(\ltrim($sTextLine), 4);
				}
				else if (!$bStart && $bIn)
				{
					$bIn = false;
					$aNextText[] = '</blockquote>';
					$aNextText[] = $sTextLine;
				}
				else if ($bStart && $bIn)
				{
					$aNextText[] = \substr(\ltrim($sTextLine), 4);
				}
				else
				{
					$aNextText[] = $sTextLine;
				}
			}

			if ($bIn)
			{
				$bIn = false;
				$aNextText[] = '</blockquote>';
			}

			$aText = $aNextText;
		}
		while ($bDo);

		$sText = \join("\n", $aText);
		unset($aText);

		$sText = \preg_replace('/[\n][ ]+/', "\n", $sText);
//		$sText = \preg_replace('/[\s]+([\s])/', '\\1', $sText);

		$sText = \preg_replace('/<blockquote>[\s]+/i', '<blockquote>', $sText);
		$sText = \preg_replace('/[\s]+<\/blockquote>/i', '</blockquote>', $sText);

		$sText = \preg_replace('/<\/blockquote>([\n]{0,2})<blockquote>/i', '\\1', $sText);
		$sText = \preg_replace('/[\n]{3,}/', "\n\n", $sText);

		$sText = \strtr($sText, array(
			"\n" => "<br />",
			"\t" => '&nbsp;&nbsp;&nbsp;',
			'  ' => '&nbsp;&nbsp;'
		));

		return $sText;
	}

	/**
	 * @param string $sText
	 *
	 * @return string
	 */
	public static function ConvertHtmlToPlain($sText)
	{
		$sText = trim(stripslashes($sText));
		$sText = preg_replace('/[\s]+/', ' ', $sText);
		$sText = preg_replace(array(
				"/\r/",
				"/[\n\t]+/",
				'/<script[^>]*>.*?<\/script>/i',
				'/<style[^>]*>.*?<\/style>/i',
				'/<title[^>]*>.*?<\/title>/i',
				'/<h[123][^>]*>(.+?)<\/h[123]>/i',
				'/<h[456][^>]*>(.+?)<\/h[456]>/i',
				'/<p[^>]*>/i',
				'/<br[^>]*>/i',
				'/<b[^>]*>(.+?)<\/b>/i',
				'/<i[^>]*>(.+?)<\/i>/i',
				'/(<ul[^>]*>|<\/ul>)/i',
				'/(<ol[^>]*>|<\/ol>)/i',
				'/<li[^>]*>/i',
				'/<a[^>]*href="([^"]+)"[^>]*>(.+?)<\/a>/i',
				'/<hr[^>]*>/i',
				'/(<table[^>]*>|<\/table>)/i',
				'/(<tr[^>]*>|<\/tr>)/i',
				'/<td[^>]*>(.+?)<\/td>/i',
				'/<th[^>]*>(.+?)<\/th>/i',
				'/&nbsp;/i',
				'/&quot;/i',
				'/&amp;/i',
				'/&copy;/i',
				'/&trade;/i',
				'/&#8220;/',
				'/&#8221;/',
				'/&#8211;/',
				'/&#8217;/',
				'/&#38;/',
				'/&#169;/',
				'/&#8482;/',
				'/&#151;/',
				'/&#147;/',
				'/&#148;/',
				'/&#149;/',
				'/&reg;/i',
				'/&bull;/i',
				'/&[&;]+;/i',
				'/&#39;/',
				'/&#160;/'
			), array(
				'',
				' ',
				'',
				'',
				'',
				"\n\n\\1\n\n",
				"\n\n\\1\n\n",
				"\n\n\t",
				"\n",
				'\\1',
				'\\1',
				"\n\n",
				"\n\n",
				"\n\t* ",
				'\\2 (\\1)',
				"\n------------------------------------\n",
				"\n",
				"\n",
				"\t\\1\n",
				"\t\\1\n",
				' ',
				'"',
				'&',
				'(c)',
				'(tm)',
				'"',
				'"',
				'-',
				"'",
				'&',
				'(c)',
				'(tm)',
				'--',
				'"',
				'"',
				'*',
				'(R)',
				'*',
				'',
				'\'',
				''
			), $sText);

		$sText = str_ireplace('<div>',"\n<div>", $sText);
		$sText = strip_tags($sText, '');
		
		// angle brackets must be replaced after the strip_tags function call to prevent replacing some text after < symbol
		$sText = preg_replace(array(
				'/&gt;/i',
				'/&lt;/i',
			), array(
				'>',
				'<',
			), $sText);
		
		$sText = preg_replace("/\n\\s+\n/", "\n", $sText);
		$sText = preg_replace("/[\n]{3,}/", "\n\n", $sText);
		return trim($sText);
	}

	public static function processNode($oNode, $iLevel)
	{
		if (self::$maxNestingLevel > 0 && $iLevel === (self::$maxNestingLevel - 1))
		{
			//Step out if maximum nesting level exceeded
			return false;
		}

		if ($oNode->nodeType === XML_ELEMENT_NODE)
		{
			if (\in_array(\strtolower($oNode->tagName), array('svg', 'head', 'link',
			'base', 'meta', 'title', 'style', 'script', 'bgsound', 'keygen', 'source',
			'object', 'embed', 'applet', 'mocha', 'iframe', 'frame', 'frameset', 'video', 'audio')) && isset($oNode->parentNode))
			{
				self::$aNodesToRemove[] = $oNode;
				return false;
			}
			else
			{
				if (self::$bCreateHtmlLinksFromTextLinksInDOM)
				{
					self::CreateHtmlLinksFromTextLinksInDOMElement($oNode);
				}
				self::clearNode($oNode);
			}
		}
		if (!$oNode->hasChildNodes()) {
			return false;
		}
		$iLevel++;
		$oNode = $oNode->firstChild;
		do {
			if ($oNode->nodeType === XML_ELEMENT_NODE || $oNode->nodeType === XML_HTML_DOCUMENT_NODE)
			{
				self::processNode($oNode, $iLevel);
			}
		}
		while($oNode = $oNode->nextSibling);
	}

	public static function clearNode($oElement)
	{

		$sTagNameLower = \strtolower($oElement->tagName);

		// convert body attributes to styles
		if ('body' === $sTagNameLower)
		{
			$aAttrs = array(
				'text' => '',
				'topmargin' => '',
				'leftmargin' => '',
				'bottommargin' => '',
				'rightmargin' => ''
			);

			if (isset($oElement->attributes))
			{
				foreach ($oElement->attributes as $sAttributeName => /* @var $oAttributeNode \DOMNode */ $oAttributeNode)
				{
					if ($oAttributeNode && isset($oAttributeNode->nodeValue))
					{
						$sAttributeNameLower = \strtolower($sAttributeName);
						if (isset($aAttrs[$sAttributeNameLower]) && '' === $aAttrs[$sAttributeNameLower])
						{
							$aAttrs[$sAttributeNameLower] = array($sAttributeName, \trim($oAttributeNode->nodeValue));
						}
					}
				}
			}

			$aStyles = array();
			foreach ($aAttrs as $sIndex => $aItem)
			{
				if (\is_array($aItem))
				{
					$oElement->removeAttribute($aItem[0]);

					switch ($sIndex)
					{
						case 'text':
							$aStyles[] = 'color: '.$aItem[1];
							break;
						case 'topmargin':
							$aStyles[] = 'margin-top: '.((int) $aItem[1]).'px';
							break;
						case 'leftmargin':
							$aStyles[] = 'margin-left: '.((int) $aItem[1]).'px';
							break;
						case 'bottommargin':
							$aStyles[] = 'margin-bottom: '.((int) $aItem[1]).'px';
							break;
						case 'rightmargin':
							$aStyles[] = 'margin-right: '.((int) $aItem[1]).'px';
							break;
					}
				}
			}

			if (0 < \count($aStyles))
			{
				$sStyles = $oElement->hasAttribute('style') ? $oElement->getAttribute('style') : '';
				$oElement->setAttribute('style', (empty($sStyles) ? '' : $sStyles.'; ').\implode('; ', $aStyles));
			}
		}

		if ('iframe' === $sTagNameLower || 'frame' === $sTagNameLower)
		{
			$oElement->setAttribute('src', 'javascript:false');
		}

		if (\in_array($sTagNameLower, array('a', 'form', 'area')))
		{
			$oElement->setAttribute('target', '_blank');
		}

		if (\in_array($sTagNameLower, array('a', 'form', 'area', 'input', 'button', 'textarea')))
		{
			$oElement->setAttribute('tabindex', '-1');
		}

		foreach (array(
			'id', 'class', 'contenteditable', 'designmode', 'formaction', 'data-bind', 'xmlns',
			'srcset'
		) as $sAttr)
		{
			@$oElement->removeAttribute($sAttr);
		}

		foreach (array(
			'load', 'blur', 'error', 'focus', 'formchange', 'change', 'start',
			'copy', 'contextmenu', 'drag', 'cut', 'paste',
			'click', 'dblclick', 'keydown', 'keypress', 'keyup',
			'mousedown', 'mouseenter', 'mouseleave', 'mousemove', 'mouseout', 'mouseover', 'mouseup',
			'move', 'resize', 'resizeend', 'resizestart', 'scroll', 'select', 'submit', 'upload'
		) as $sAttr)
		{
			@$oElement->removeAttribute('on'.$sAttr);
		}

		$aNotJsAttrs = ['href', 'action'];
		foreach ($aNotJsAttrs as $sAttrName)
		{
			if ($oElement->hasAttribute($sAttrName))
			{
				$sHref = \trim($oElement->getAttribute($sAttrName));
				if (substr($sHref, 0, 11) === 'javascript:')
				{
					$oElement->setAttribute($sAttrName, 'javascript:void(0)');
				}
				else if ('a' === $sTagNameLower)
				{
					$oElement->setAttribute('rel', 'external');
					$oElement->setAttribute('class', 'external');
				}
			}
		}

		if ($oElement->hasAttribute('src'))
		{
			$sSrc = \trim($oElement->getAttribute('src'));
			$oElement->removeAttribute('src');

			if (\in_array($sSrc, self::$aContentLocationUrls))
			{
				$oElement->setAttribute('data-x-src-location', $sSrc);
				self::$aFoundedContentLocationUrls[] = $sSrc;
			}
			else if ('cid:' === \strtolower(\substr($sSrc, 0, 4)))
			{
				$oElement->setAttribute('data-x-src-cid', \substr($sSrc, 4));
				self::$aFoundCIDs[] = \substr($sSrc, 4);
			}
			else
			{
				if (\preg_match('/http[s]?:\/\//i', $sSrc))
				{
					if (self::$bDoNotReplaceExternalUrl)
					{
						$oElement->setAttribute('src', $sSrc);
					}
					else
					{
						$oElement->setAttribute('data-x-src', $sSrc);
						if (self::$fAdditionalExternalFilter)
						{
							$sCallResult = \call_user_func(self::$fAdditionalExternalFilter, $sSrc);
							if (0 < \strlen($sCallResult))
							{
								$oElement->setAttribute('data-x-additional-src', $sCallResult);
							}
						}
					}

					self::$bHasExternals = true;
				}
				else if ('data:image/' === \strtolower(\substr(\trim($sSrc), 0, 11)))
				{
					$oElement->setAttribute('src', $sSrc);
				}
				else
				{
					$oElement->setAttribute('data-x-broken-src', $sSrc);
				}
			}
		}

		$sBackground = $oElement->hasAttribute('background')
			? \trim($oElement->getAttribute('background')) : '';
		$sBackgroundColor = $oElement->hasAttribute('bgcolor')
			? \trim($oElement->getAttribute('bgcolor')) : '';

		if (!empty($sBackground) || !empty($sBackgroundColor))
		{
			$aStyles = array();
			$sStyles = $oElement->hasAttribute('style')
				? $oElement->getAttribute('style') : '';

			if (!empty($sBackground))
			{
				$aStyles[] = 'background-image: url(\''.$sBackground.'\')';
				$oElement->removeAttribute('background');
			}

			if (!empty($sBackgroundColor))
			{
				$aStyles[] = 'background-color: '.$sBackgroundColor;
				$oElement->removeAttribute('bgcolor');
			}

			$oElement->setAttribute('style', (empty($sStyles) ? '' : $sStyles.'; ').\implode('; ', $aStyles));
		}

		if ($oElement->hasAttribute('style'))
		{
			$oElement->setAttribute('style',
				\MailSo\Base\HtmlUtils::ClearStyle($oElement->getAttribute('style'), $oElement, self::$bHasExternals,
					self::$aFoundCIDs, self::$aContentLocationUrls, self::$aFoundedContentLocationUrls, self::$bDoNotReplaceExternalUrl, self::$fAdditionalExternalFilter));
		}

		if ($oElement->hasAttribute('cursor'))
		{
			$sCursor = $oElement->getAttribute('style');
			if (strpos($sCursor, 'url(') !== false)
			{
				$oElement->removeAttribute('cursor');
			}
		}
	}
}
