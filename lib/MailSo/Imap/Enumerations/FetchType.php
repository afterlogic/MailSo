<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Imap\Enumerations;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Imap
 * @subpackage Enumerations
 */
class FetchType
{
    public const ALL = 'ALL';
    public const FAST = 'FAST';
    public const FULL = 'FULL';
    public const BODY = 'BODY';
    public const BODY_PEEK = 'BODY.PEEK';
    public const BODY_HEADER = 'BODY[HEADER]';
    public const BODY_HEADER_PEEK = 'BODY.PEEK[HEADER]';
    public const BODYSTRUCTURE = 'BODYSTRUCTURE';
    public const ENVELOPE = 'ENVELOPE';
    public const FLAGS = 'FLAGS';
    public const INTERNALDATE = 'INTERNALDATE';
    public const RFC822 = 'RFC822';
    public const RFC822_HEADER = 'RFC822.HEADER';
    public const RFC822_SIZE = 'RFC822.SIZE';
    public const RFC822_TEXT = 'RFC822.TEXT';
    public const UID = 'UID';
    public const INDEX = 'INDEX';

    public const GMAIL_MSGID = 'X-GM-MSGID';
    public const GMAIL_THRID = 'X-GM-THRID';
    public const GMAIL_LABELS = 'X-GM-LABELS';

    /**
     * @param array $aReturn
     *
     * @param string|array $mType
     */
    private static function addHelper(&$aReturn, $mType)
    {
        if (\is_string($mType)) {
            $aReturn[$mType] = '';
        } elseif (\is_array($mType) && 2 === count($mType) && \is_string($mType[0]) &&
            is_callable($mType[1])) {
            $aReturn[$mType[0]] = $mType[1];
        }
    }

    /**
     * @param array $aHeaders
     * @param bool $bPeek = true
     *
     * @return string
     */
    public static function BuildBodyCustomHeaderRequest(array $aHeaders, $bPeek = true)
    {
        $sResult = '';
        if (0 < \count($aHeaders)) {
            $aHeaders = \array_map('trim', $aHeaders);
            $aHeaders = \array_map('strtoupper', $aHeaders);

            $sResult = $bPeek ? self::BODY_PEEK : self::BODY;
            $sResult .= '[HEADER.FIELDS ('.\implode(' ', $aHeaders).')]';
        }

        return $sResult;
    }

    /**
     * @param array $aFetchItems
     *
     * @return array
     */
    public static function ChangeFetchItemsBefourRequest(array $aFetchItems)
    {
        $aReturn = array();
        self::addHelper($aReturn, self::UID);
        self::addHelper($aReturn, self::RFC822_SIZE);

        foreach ($aFetchItems as $mFetchKey) {
            switch ($mFetchKey) {
                default:
                    if (is_string($mFetchKey) || is_array($mFetchKey)) {
                        self::addHelper($aReturn, $mFetchKey);
                    }
                    break;
                case self::INDEX:
                case self::UID:
                case self::RFC822_SIZE:
                    break;
                case self::ALL:
                    self::addHelper($aReturn, self::FLAGS);
                    self::addHelper($aReturn, self::INTERNALDATE);
                    self::addHelper($aReturn, self::ENVELOPE);
                    break;
                case self::FAST:
                    self::addHelper($aReturn, self::FLAGS);
                    self::addHelper($aReturn, self::INTERNALDATE);
                    break;
                case self::FULL:
                    self::addHelper($aReturn, self::FLAGS);
                    self::addHelper($aReturn, self::INTERNALDATE);
                    self::addHelper($aReturn, self::ENVELOPE);
                    self::addHelper($aReturn, self::BODY);
                    break;
            }
        }

        return $aReturn;
    }
}
