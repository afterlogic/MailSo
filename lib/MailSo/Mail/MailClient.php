<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Mail;

/**
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0
 * @license https://afterlogic.com/products/common-licensing Afterlogic Software License
 * @copyright Copyright (c) 2019, Afterlogic Corp.
 *
 * @category MailSo
 * @package Mail
 */
class MailClient
{
    /**
     * @var \MailSo\Log\Logger
     */
    private $oLogger;

    /**
     * @var \MailSo\Imap\ImapClient
     */
    private $oImapClient;

    /**
     * @access private
     */
    private function __construct()
    {
        $this->oLogger = null;

        $this->oImapClient = \MailSo\Imap\ImapClient::NewInstance();
        $this->oImapClient->SetTimeOuts(10, 300); // TODO
    }

    /**
     * @return \MailSo\Mail\MailClient
     */
    public static function NewInstance()
    {
        return new self();
    }

    /**
     * @return \MailSo\Imap\ImapClient
     */
    public function ImapClient()
    {
        return $this->oImapClient;
    }

    /**
     * @param string $sServerName
     * @param int $iPort = 143
     * @param int $iSecurityType = \MailSo\Net\Enumerations\ConnectionSecurityType::AUTO_DETECT
     * @param bool $bVerifySsl = false
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function Connect(
        $sServerName,
        $iPort = 143,
        $iSecurityType = \MailSo\Net\Enumerations\ConnectionSecurityType::AUTO_DETECT,
        $bVerifySsl = false
    ) {
        $this->oImapClient->Connect($sServerName, $iPort, $iSecurityType, $bVerifySsl);
        return $this;
    }

    /**
     * @param string $sLogin
     * @param string $sPassword
     * @param string $sProxyAuthUser = ''
     * @param bool $bUseAuthPlainIfSupported = false
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\LoginException
     */
    public function Login($sLogin, $sPassword, $sProxyAuthUser = '', $bUseAuthPlainIfSupported = false)
    {
        $this->oImapClient->Login($sLogin, $sPassword, $sProxyAuthUser, $bUseAuthPlainIfSupported);
        return $this;
    }

    /**
     * @param string $sXOAuth2Token
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\LoginException
     */
    public function LoginWithXOauth2($sXOAuth2Token)
    {
        $this->oImapClient->LoginWithXOauth2($sXOAuth2Token);
        return $this;
    }

    /**
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Net\Exceptions\Exception
     */
    public function Logout()
    {
        $this->oImapClient->Logout();
        return $this;
    }

    /**
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Net\Exceptions\Exception
     */
    public function Disconnect()
    {
        $this->oImapClient->Disconnect();
        return $this;
    }

    /**
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Net\Exceptions\Exception
     */
    public function LogoutAndDisconnect()
    {
        return $this->Logout()->Disconnect();
    }

    /**
     * @return bool
     */
    public function IsConnected()
    {
        return $this->oImapClient->IsConnected();
    }

    /**
     * @return bool
     */
    public function IsLoggined()
    {
        return $this->oImapClient->IsLoggined();
    }

    /**
     * @return string
     */
    private function getEnvelopeOrHeadersRequestString()
    {
        return \MailSo\Imap\Enumerations\FetchType::BODY_HEADER_PEEK;

        //		return \MailSo\Imap\Enumerations\FetchType::ENVELOPE;
        //
        //		return \MailSo\Imap\Enumerations\FetchType::BuildBodyCustomHeaderRequest(array(
        //			\MailSo\Mime\Enumerations\Header::RETURN_PATH,
        //			\MailSo\Mime\Enumerations\Header::RECEIVED,
        //			\MailSo\Mime\Enumerations\Header::MIME_VERSION,
        //			\MailSo\Mime\Enumerations\Header::MESSAGE_ID,
        //			\MailSo\Mime\Enumerations\Header::FROM_,
        //			\MailSo\Mime\Enumerations\Header::TO_,
        //			\MailSo\Mime\Enumerations\Header::CC,
        //			\MailSo\Mime\Enumerations\Header::BCC,
        //			\MailSo\Mime\Enumerations\Header::SENDER,
        //			\MailSo\Mime\Enumerations\Header::REPLY_TO,
        //			\MailSo\Mime\Enumerations\Header::IN_REPLY_TO,
        //			\MailSo\Mime\Enumerations\Header::DATE,
        //			\MailSo\Mime\Enumerations\Header::SUBJECT,
        //			\MailSo\Mime\Enumerations\Header::X_MSMAIL_PRIORITY,
        //			\MailSo\Mime\Enumerations\Header::IMPORTANCE,
        //			\MailSo\Mime\Enumerations\Header::X_PRIORITY,
        //			\MailSo\Mime\Enumerations\Header::CONTENT_TYPE,
        //			\MailSo\Mime\Enumerations\Header::REFERENCES,
        //			\MailSo\Mime\Enumerations\Header::X_DRAFT_INFO,
        //			\MailSo\Mime\Enumerations\Header::RECEIVED_SPF,
        //			\MailSo\Mime\Enumerations\Header::AUTHENTICATION_RESULTS,
        //		), true);
    }

    /**
     * @param string $sFolderName
     * @param string $sMessageFlag
     * @param bool $bSetAction = true
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     * @throws \MailSo\Mail\Exceptions\Exception
     */
    public function MessageSetFlagToAll($sFolderName, $sMessageFlag, $bSetAction = true)
    {
        $this->oImapClient->FolderSelect($sFolderName);

        $oFolderInfo = $this->oImapClient->FolderCurrentInformation();
        if (!$oFolderInfo || !$oFolderInfo->IsFlagSupported($sMessageFlag)) {
            throw new \MailSo\Mail\Exceptions\RuntimeException('Message flag is not supported.');
        }

        if (0 < $oFolderInfo->Exists) {
            $sStoreAction = $bSetAction
                ? \MailSo\Imap\Enumerations\StoreAction::ADD_FLAGS_SILENT
                : \MailSo\Imap\Enumerations\StoreAction::REMOVE_FLAGS_SILENT
            ;

            $this->oImapClient->MessageStoreFlag('1:*', false, array($sMessageFlag), $sStoreAction);
        }
    }

    /**
     * @param string $sFolderName
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     * @param string $sMessageFlag
     * @param bool $bSetAction = true
     * @param bool $sSkipUnsupportedFlag = false
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     * @throws \MailSo\Mail\Exceptions\Exception
     */
    public function MessageSetFlag($sFolderName, $aIndexRange, $bIndexIsUid, $sMessageFlag, $bSetAction = true, $sSkipUnsupportedFlag = false)
    {
        $this->oImapClient->FolderSelect($sFolderName);

        $oFolderInfo = $this->oImapClient->FolderCurrentInformation();
        if (!$oFolderInfo || !$oFolderInfo->IsFlagSupported($sMessageFlag)) {
            if (!$sSkipUnsupportedFlag) {
                throw new \MailSo\Mail\Exceptions\RuntimeException('Message flag is not supported.');
            }
        } else {
            $sStoreAction = $bSetAction
                ? \MailSo\Imap\Enumerations\StoreAction::ADD_FLAGS_SILENT
                : \MailSo\Imap\Enumerations\StoreAction::REMOVE_FLAGS_SILENT
            ;

            $this->oImapClient->MessageStoreFlag(
                \MailSo\Base\Utils::PrepearFetchSequence($aIndexRange),
                $bIndexIsUid,
                array($sMessageFlag),
                $sStoreAction
            );
        }
    }

    /**
     * @param string $sFolderName
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     * @param bool $bSetAction = true
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageSetFlagged($sFolderName, $aIndexRange, $bIndexIsUid, $bSetAction = true)
    {
        $this->MessageSetFlag(
            $sFolderName,
            $aIndexRange,
            $bIndexIsUid,
            \MailSo\Imap\Enumerations\MessageFlag::FLAGGED,
            $bSetAction
        );
    }

    /**
     * @param string $sFolderName
     * @param bool $bSetAction = true
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageSetSeenToAll($sFolderName, $bSetAction = true)
    {
        $this->MessageSetFlagToAll($sFolderName, \MailSo\Imap\Enumerations\MessageFlag::SEEN, $bSetAction);
    }

    /**
     * @param string $sFolderName
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     * @param bool $bSetAction = true
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageSetSeen($sFolderName, $aIndexRange, $bIndexIsUid, $bSetAction = true)
    {
        $this->MessageSetFlag(
            $sFolderName,
            $aIndexRange,
            $bIndexIsUid,
            \MailSo\Imap\Enumerations\MessageFlag::SEEN,
            $bSetAction
        );
    }

    /**
     * @param string $sFolderName
     * @param int $iIndex
     * @param bool $bIndexIsUid = true
     * @param \MailSo\Cache\CacheClient $oCacher = null
     * @param int $iBodyTextLimit = null
     *
     * @return \MailSo\Mail\Message|false
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function Message($sFolderName, $iIndex, $bIndexIsUid = true, $oCacher = null, $iBodyTextLimit = null)
    {
        if (!\MailSo\Base\Validator::RangeInt($iIndex, 1)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderExamine($sFolderName);

        $oBodyStructure = null;
        $oMessage = false;

        $aBodyPeekMimeIndexes = array();
        $aSignatureMimeIndexes = array();

        $aFetchResponse = $this->oImapClient->Fetch(array(\MailSo\Imap\Enumerations\FetchType::BODYSTRUCTURE), $iIndex, $bIndexIsUid);
        if (0 < \count($aFetchResponse) && isset($aFetchResponse[0])) {
            $oBodyStructure = $aFetchResponse[0]->GetFetchBodyStructure();
            if ($oBodyStructure) {
                $aTextParts = $oBodyStructure->SearchHtmlOrPlainParts();
                if (is_array($aTextParts) && 0 < \count($aTextParts)) {
                    foreach ($aTextParts as $oPart) {
                        $aBodyPeekMimeIndexes[] = array($oPart->PartID(), $oPart->Size());
                    }
                }

                $aSignatureParts = $oBodyStructure->SearchByContentType('application/pgp-signature');
                if (is_array($aSignatureParts) && 0 < \count($aSignatureParts)) {
                    foreach ($aSignatureParts as $oPart) {
                        $aSignatureMimeIndexes[] = $oPart->PartID();
                    }
                }
            }
        }

        $aFetchItems = array(
            \MailSo\Imap\Enumerations\FetchType::INDEX,
            \MailSo\Imap\Enumerations\FetchType::UID,
            \MailSo\Imap\Enumerations\FetchType::RFC822_SIZE,
            \MailSo\Imap\Enumerations\FetchType::INTERNALDATE,
            \MailSo\Imap\Enumerations\FetchType::FLAGS,
            $this->getEnvelopeOrHeadersRequestString()
        );

        if (0 < \count($aBodyPeekMimeIndexes)) {
            foreach ($aBodyPeekMimeIndexes as $aTextMimeData) {
                $sLine = \MailSo\Imap\Enumerations\FetchType::BODY_PEEK.'['.$aTextMimeData[0].']';
                if (\is_numeric($iBodyTextLimit) && 0 < $iBodyTextLimit && $iBodyTextLimit < $aTextMimeData[1]) {
                    $sLine .= '<0.'.((int) $iBodyTextLimit).'>';
                }

                $aFetchItems[] = $sLine;
            }
        }

        if (0 < \count($aSignatureMimeIndexes)) {
            foreach ($aSignatureMimeIndexes as $sTextMimeIndex) {
                $aFetchItems[] = \MailSo\Imap\Enumerations\FetchType::BODY_PEEK.'['.$sTextMimeIndex.']';
            }
        }

        if (!$oBodyStructure) {
            $aFetchItems[] = \MailSo\Imap\Enumerations\FetchType::BODYSTRUCTURE;
        }

        $aFetchResponse = $this->oImapClient->Fetch($aFetchItems, $iIndex, $bIndexIsUid);
        if (0 < \count($aFetchResponse)) {
            $oMessage = \MailSo\Mail\Message::NewFetchResponseInstance(
                $sFolderName,
                $aFetchResponse[0],
                $oBodyStructure
            );
        }

        return $oMessage;
    }

    /**
     * @param mixed $mCallback
     * @param string $sFolderName
     * @param int $iIndex
     * @param bool $bIndexIsUid = true,
     * @param string $sMimeIndex = ''
     *
     * @return bool
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageMimeStream($mCallback, $sFolderName, $iIndex, $bIndexIsUid = true, $sMimeIndex = '')
    {
        if (!is_callable($mCallback)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderExamine($sFolderName);

        $sFileName = '';
        $sContentType = '';
        $sMailEncodingName = '';

        $sMimeIndex = trim($sMimeIndex);
        $aFetchResponse = $this->oImapClient->Fetch(array(
            0 === \strlen($sMimeIndex)
                ? \MailSo\Imap\Enumerations\FetchType::BODY_HEADER_PEEK
                : \MailSo\Imap\Enumerations\FetchType::BODY_PEEK.'['.$sMimeIndex.'.MIME]'
        ), $iIndex, $bIndexIsUid);

        if (0 < \count($aFetchResponse)) {
            $sMime = $aFetchResponse[0]->GetFetchValue(
                0 === \strlen($sMimeIndex)
                    ? \MailSo\Imap\Enumerations\FetchType::BODY_HEADER
                    : \MailSo\Imap\Enumerations\FetchType::BODY.'['.$sMimeIndex.'.MIME]'
            );

            if (0 < \strlen($sMime)) {
                $oHeaders = \MailSo\Mime\HeaderCollection::NewInstance()->Parse($sMime);

                if (0 < \strlen($sMimeIndex)) {
                    $sFileName = $oHeaders->ParameterValue(
                        \MailSo\Mime\Enumerations\Header::CONTENT_DISPOSITION,
                        \MailSo\Mime\Enumerations\Parameter::FILENAME
                    );

                    if (0 === \strlen($sFileName)) {
                        $sFileName = $oHeaders->ParameterValue(
                            \MailSo\Mime\Enumerations\Header::CONTENT_TYPE,
                            \MailSo\Mime\Enumerations\Parameter::NAME
                        );
                    }

                    $sMailEncodingName = $oHeaders->ValueByName(
                        \MailSo\Mime\Enumerations\Header::CONTENT_TRANSFER_ENCODING
                    );

                    $sContentType = $oHeaders->ValueByName(
                        \MailSo\Mime\Enumerations\Header::CONTENT_TYPE
                    );
                } else {
                    $sSubject = $oHeaders->ValueByName(\MailSo\Mime\Enumerations\Header::SUBJECT);

                    $sFileName = 0 === \strlen($sSubject) ? (string) $iIndex : $sSubject;
                    $sFileName .= '.eml';

                    $sContentType = 'message/rfc822';
                }
            }
        }

        $aFetchResponse = $this->oImapClient->Fetch(array(
            array(\MailSo\Imap\Enumerations\FetchType::BODY_PEEK.'['.$sMimeIndex.']',
                function ($sParent, $sLiteralAtomUpperCase, $rImapLiteralStream) use ($mCallback, $sMimeIndex, $sMailEncodingName, $sContentType, $sFileName) {
                    if (0 < \strlen($sLiteralAtomUpperCase)) {
                        if (is_resource($rImapLiteralStream) && 'FETCH' === $sParent) {
                            $rMessageMimeIndexStream = (0 === \strlen($sMailEncodingName))
                                ? $rImapLiteralStream
                                : \MailSo\Base\StreamWrappers\Binary::CreateStream(
                                    $rImapLiteralStream,
                                    \MailSo\Base\StreamWrappers\Binary::GetInlineDecodeOrEncodeFunctionName(
                                        $sMailEncodingName,
                                        true
                                    )
                                );

                            \call_user_func($mCallback, $rMessageMimeIndexStream, $sContentType, $sFileName, $sMimeIndex);
                        }
                    }
                }
            )), $iIndex, $bIndexIsUid);

        return ($aFetchResponse && 1 === \count($aFetchResponse));
    }

    /**
     * @param string $sFolder
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     * @param bool $bUseExpunge = true
     * @param bool $bExpungeAll = false
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageDelete($sFolder, $aIndexRange, $bIndexIsUid, $bUseExpunge = true, $bExpungeAll = false)
    {
        if (0 === \strlen($sFolder) || !\is_array($aIndexRange) || 0 === \count($aIndexRange)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderSelect($sFolder);

        $sIndexRange = \MailSo\Base\Utils::PrepearFetchSequence($aIndexRange);

        $this->oImapClient->MessageStoreFlag(
            $sIndexRange,
            $bIndexIsUid,
            array(\MailSo\Imap\Enumerations\MessageFlag::DELETED),
            \MailSo\Imap\Enumerations\StoreAction::ADD_FLAGS_SILENT
        );

        if ($bUseExpunge) {
            $this->oImapClient->MessageExpunge($bIndexIsUid ? $sIndexRange : '', $bIndexIsUid, $bExpungeAll);
        }

        return $this;
    }

    /**
     * @param string $sFromFolder
     * @param string $sToFolder
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     * @param bool $bUseMoveSupported = false
     * @param bool $bExpungeAll = false
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageMove($sFromFolder, $sToFolder, $aIndexRange, $bIndexIsUid, $bUseMoveSupported = false, $bExpungeAll = false)
    {
        if (0 === \strlen($sFromFolder) || 0 === \strlen($sToFolder) ||
            !\is_array($aIndexRange) || 0 === \count($aIndexRange)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderSelect($sFromFolder);

        if ($bUseMoveSupported && $this->oImapClient->IsSupported('MOVE')) {
            $this->oImapClient->MessageMove(
                $sToFolder,
                \MailSo\Base\Utils::PrepearFetchSequence($aIndexRange),
                $bIndexIsUid
            );
        } else {
            $this->oImapClient->MessageCopy(
                $sToFolder,
                \MailSo\Base\Utils::PrepearFetchSequence($aIndexRange),
                $bIndexIsUid
            );

            $this->MessageDelete($sFromFolder, $aIndexRange, $bIndexIsUid, true, $bExpungeAll);
        }

        return $this;
    }

    /**
     * @param string $sFromFolder
     * @param string $sToFolder
     * @param array $aIndexRange
     * @param bool $bIndexIsUid
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageCopy($sFromFolder, $sToFolder, $aIndexRange, $bIndexIsUid)
    {
        if (0 === \strlen($sFromFolder) || 0 === \strlen($sToFolder) ||
            !\is_array($aIndexRange) || 0 === \count($aIndexRange)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderSelect($sFromFolder);
        $this->oImapClient->MessageCopy(
            $sToFolder,
            \MailSo\Base\Utils::PrepearFetchSequence($aIndexRange),
            $bIndexIsUid
        );

        return $this;
    }

    /**
     * @param resource $rMessageStream
     * @param int $iMessageStreamSize
     * @param string $sFolderToSave
     * @param array $aAppendFlags = null
     * @param int $iUid = null
     *
     * @return \MailSo\Mail\MailClient
     */
    public function MessageAppendStream($rMessageStream, $iMessageStreamSize, $sFolderToSave, $aAppendFlags = null, &$iUid = null)
    {
        if (!\is_resource($rMessageStream) || 0 === \strlen($sFolderToSave)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->MessageAppendStream(
            $sFolderToSave,
            $rMessageStream,
            $iMessageStreamSize,
            $aAppendFlags,
            $iUid
        );

        return $this;
    }

    /**
     * @param string $sMessageFileName
     * @param string $sFolderToSave
     * @param array $aAppendFlags = null
     * @param int &$iUid = null
     *
     * @return \MailSo\Mail\MailClient
     */
    public function MessageAppendFile($sMessageFileName, $sFolderToSave, $aAppendFlags = null, &$iUid = null)
    {
        if (!@\is_file($sMessageFileName) || !@\is_readable($sMessageFileName)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $iMessageStreamSize = \filesize($sMessageFileName);
        $rMessageStream = \fopen($sMessageFileName, 'rb');

        $this->MessageAppendStream($rMessageStream, $iMessageStreamSize, $sFolderToSave, $aAppendFlags, $iUid);

        if (\is_resource($rMessageStream)) {
            @fclose($rMessageStream);
        }

        return $this;
    }

    /**
     * @param string $sFolderName
     * @param int $iCount
     * @param int $iUnseenCount
     * @param string $sUidNext
     *
     * @return void
     */
    protected function initFolderValues($sFolderName, &$iCount, &$iUnseenCount, &$sUidNext)
    {
        $aFolderStatus = $this->oImapClient->FolderStatus($sFolderName, array(
            \MailSo\Imap\Enumerations\FolderResponseStatus::MESSAGES,
            \MailSo\Imap\Enumerations\FolderResponseStatus::UNSEEN,
            \MailSo\Imap\Enumerations\FolderResponseStatus::UIDNEXT
        ));

        $iCount = isset($aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::MESSAGES])
            ? (int) $aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::MESSAGES] : 0;

        $iUnseenCount = isset($aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::UNSEEN])
            ? (int) $aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::UNSEEN] : 0;

        $sUidNext = isset($aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::UIDNEXT])
            ? (string) $aFolderStatus[\MailSo\Imap\Enumerations\FolderResponseStatus::UIDNEXT] : '0';

        if ($this->IsGmail()) {
            $oFolder = $this->oImapClient->FolderCurrentInformation();
            if ($oFolder && null !== $oFolder->Exists && $oFolder->FolderName === $sFolderName) {
                $iSubCount = (int) $oFolder->Exists;
                if (0 < $iSubCount && $iSubCount < $iCount) {
                    $iCount = $iSubCount;
                }
            }
        }
    }

    /**
     * @param string $sFolder
     * @param int $iCount
     * @param int $iUnseenCount
     * @param string $sUidNext
     *
     * @return string
     */
    public static function GenerateHash($sFolder, $iCount, $iUnseenCount, $sUidNext)
    {
        $iUnseenCount = 0; // unneccessery
        return \md5($sFolder.'-'.$iCount.'-'.$iUnseenCount.'-'.$sUidNext);
    }

    /**
     * @param string $sFolderName
     * @param string $sPrevUidNext
     * @param string $sCurrentUidNext
     *
     * @return array
     */
    private function getFolderNextMessageInformation($sFolderName, $sPrevUidNext, $sCurrentUidNext)
    {
        $aNewMessages = array();

        if (0 < \strlen($sPrevUidNext) && (string) $sPrevUidNext !== (string) $sCurrentUidNext) {
            $this->oImapClient->FolderExamine($sFolderName);

            $aFetchResponse = $this->oImapClient->Fetch(array(
                \MailSo\Imap\Enumerations\FetchType::INDEX,
                \MailSo\Imap\Enumerations\FetchType::UID,
                \MailSo\Imap\Enumerations\FetchType::FLAGS,
                \MailSo\Imap\Enumerations\FetchType::BuildBodyCustomHeaderRequest(array(
                    \MailSo\Mime\Enumerations\Header::FROM_,
                    \MailSo\Mime\Enumerations\Header::SUBJECT,
                    \MailSo\Mime\Enumerations\Header::CONTENT_TYPE
                ))
            ), $sPrevUidNext.':*', true);

            if (\is_array($aFetchResponse) && 0 < \count($aFetchResponse)) {
                foreach ($aFetchResponse as /* @var $oFetchResponse \MailSo\Imap\FetchResponse */ $oFetchResponse) {
                    $aFlags = \array_map('strtolower', $oFetchResponse->GetFetchValue(
                        \MailSo\Imap\Enumerations\FetchType::FLAGS
                    ));

                    if (\in_array(\strtolower(\MailSo\Imap\Enumerations\MessageFlag::RECENT), $aFlags) ||
                        !\in_array(\strtolower(\MailSo\Imap\Enumerations\MessageFlag::SEEN), $aFlags)) {
                        $sUid = $oFetchResponse->GetFetchValue(\MailSo\Imap\Enumerations\FetchType::UID);
                        $sHeaders = $oFetchResponse->GetHeaderFieldsValue();

                        $oHeaders = \MailSo\Mime\HeaderCollection::NewInstance()->Parse($sHeaders);

                        $sContentTypeCharset = $oHeaders->ParameterValue(
                            \MailSo\Mime\Enumerations\Header::CONTENT_TYPE,
                            \MailSo\Mime\Enumerations\Parameter::CHARSET
                        );

                        $sCharset = '';
                        if (0 < \strlen($sContentTypeCharset)) {
                            $sCharset = $sContentTypeCharset;
                        }

                        if (0 < \strlen($sCharset)) {
                            $oHeaders->SetParentCharset($sCharset);
                        }

                        $aNewMessages[] = array(
                            'Folder' => $sFolderName,
                            'Uid' => $sUid,
                            'Subject' => $oHeaders->ValueByName(\MailSo\Mime\Enumerations\Header::SUBJECT, 0 === \strlen($sCharset)),
                            'From' => $oHeaders->GetAsEmailCollection(\MailSo\Mime\Enumerations\Header::FROM_, 0 === \strlen($sCharset))
                        );
                    }
                }
            }
        }

        return $aNewMessages;
    }

    /**
     * @param string $sFolderName
     * @param string $sPrevUidNext = ''
     * @param array $aUids = ''
     *
     * @return string
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function FolderInformation($sFolderName, $sPrevUidNext = '', $aUids = array())
    {
        $aFlags = array();

        $bSelect = false;
        if ($this->IsGmail()) {
            $this->oImapClient->FolderExamine($sFolderName);
            $bSelect = true;
        }

        if (\is_array($aUids) && 0 < \count($aUids)) {
            if (!$bSelect) {
                $this->oImapClient->FolderExamine($sFolderName);
            }

            $aFetchResponse = $this->oImapClient->Fetch(array(
                \MailSo\Imap\Enumerations\FetchType::INDEX,
                \MailSo\Imap\Enumerations\FetchType::UID,
                \MailSo\Imap\Enumerations\FetchType::FLAGS
            ), \MailSo\Base\Utils::PrepearFetchSequence($aUids), true);

            if (\is_array($aFetchResponse) && 0 < \count($aFetchResponse)) {
                foreach ($aFetchResponse as $oFetchResponse) {
                    $sUid = $oFetchResponse->GetFetchValue(\MailSo\Imap\Enumerations\FetchType::UID);
                    $aFlags[(\is_numeric($sUid) ? (int) $sUid : 0)] =
                        $oFetchResponse->GetFetchValue(\MailSo\Imap\Enumerations\FetchType::FLAGS);
                }
            }
        }

        $iCount = 0;
        $iUnseenCount = 0;
        $sUidNext = '0';

        $this->initFolderValues($sFolderName, $iCount, $iUnseenCount, $sUidNext);

        $aResult = array(
            'Folder' => $sFolderName,
            'Hash' => self::GenerateHash($sFolderName, $iCount, $iUnseenCount, $sUidNext),
            'MessageCount' => $iCount,
            'MessageUnseenCount' => $iUnseenCount,
            'UidNext' => $sUidNext,
            'Flags' => $aFlags,
            'NewMessages' => 'INBOX' === $sFolderName ?
                $this->getFolderNextMessageInformation($sFolderName, $sPrevUidNext, $sUidNext) : array()
        );

        return $aResult;
    }

    /**
     * @param string $sFolderName
     *
     * @return string
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function FolderHash($sFolderName)
    {
        $iCount = 0;
        $iUnseenCount = 0;
        $sUidNext = '0';

        $this->initFolderValues($sFolderName, $iCount, $iUnseenCount, $sUidNext);

        return self::GenerateHash($sFolderName, $iCount, $iUnseenCount, $sUidNext);
    }

    /**
     *
     * @return string
     */
    public function IsGmail()
    {
        return 'ssl://imap.gmail.com' === \strtolower($this->oImapClient->GetConnectedHost());
    }

    /**
     * @param string $sSearch
     * @param bool $bDetectGmail = true
     *
     * @return string
     */
    private function escapeSearchString($sSearch, $bDetectGmail = true)
    {
        return ($bDetectGmail && !\MailSo\Base\Utils::IsAscii($sSearch) && $this->IsGmail())
            ? '{'.\strlen($sSearch).'+}'."\r\n".$sSearch : $this->oImapClient->EscapeString($sSearch);
    }

    /**
     * @param string $sDate
     * @param int $iTimeZoneOffset
     *
     * @return int
     */
    private function parseSearchDate($sDate, $iTimeZoneOffset)
    {
        $iResult = 0;
        if (0 < \strlen($sDate)) {
            $oDateTime = \DateTime::createFromFormat('Y.m.d', $sDate, \MailSo\Base\DateTimeHelper::GetUtcTimeZoneObject());
            return $oDateTime ? $oDateTime->getTimestamp() - $iTimeZoneOffset : 0;
        }

        return $iResult;
    }

    /**
     * @param string $sSize
     *
     * @return int
     */
    private function parseFriendlySize($sSize)
    {
        $sSize = preg_replace('/[^0-9bBkKmM]/', '', $sSize);

        $iResult = 0;
        $aMatch = array();

        if (\preg_match('/([\d]+)(B|KB|M|MB|G|GB)$/i', $sSize, $aMatch) && isset($aMatch[1], $aMatch[2])) {
            $iResult = (int) $aMatch[1];
            switch (\strtoupper($aMatch[2])) {
                case 'K':
                case 'KB':
                    $iResult *= 1024;
                    // no break
                case 'M':
                case 'MB':
                    $iResult *= 1024;
                    // no break
                case 'G':
                case 'GB':
                    $iResult *= 1024;
            }
        } else {
            $iResult = (int) $sSize;
        }

        return $iResult;
    }

    /**
     * @param string $sSearch
     *
     * @return array
     */
    private function parseSearchString($sSearch)
    {
        $aResult = array(
            'OTHER' => ''
        );

        $aCache = array();

        $sReg = 'e?mail|from|to|subject|has|is|date|text|body|size|larger|bigger|smaller|maxsize|minsize';

        $sSearch = \trim(\preg_replace('/[\s]+/', ' ', $sSearch));
        $sSearch = \trim(\preg_replace('/('.$sReg.'): /i', '\\1:', $sSearch));

        $mMatch = array();
        \preg_match_all('/".*?(?<!\\\)"/', $sSearch, $mMatch);
        if (\is_array($mMatch) && isset($mMatch[0]) && \is_array($mMatch[0]) && 0 < \count($mMatch[0])) {
            foreach ($mMatch[0] as $sItem) {
                do {
                    $sKey = \md5(\rand(10000, 90000).\microtime(true));
                } while (isset($aCache[$sKey]));

                $aCache[$sKey] = \stripcslashes($sItem);
                $sSearch = \str_replace($sItem, $sKey, $sSearch);
            }
        }

        \preg_match_all('/\'.*?(?<!\\\)\'/', $sSearch, $mMatch);
        if (\is_array($mMatch) && isset($mMatch[0]) && \is_array($mMatch[0]) && 0 < \count($mMatch[0])) {
            foreach ($mMatch[0] as $sItem) {
                do {
                    $sKey = \md5(\rand(10000, 90000).\microtime(true));
                } while (isset($aCache[$sKey]));

                $aCache[$sKey] = \stripcslashes($sItem);
                $sSearch = \str_replace($sItem, $sKey, $sSearch);
            }
        }

        $mMatch = array();
        \preg_match_all('/('.$sReg.'):([^\s]*)/i', $sSearch, $mMatch);
        if (\is_array($mMatch) && isset($mMatch[1]) && \is_array($mMatch[1]) && 0 < \count($mMatch[1])) {
            if (\is_array($mMatch[0])) {
                foreach ($mMatch[0] as $sToken) {
                    $sSearch = \str_replace($sToken, '', $sSearch);
                }

                $sSearch = \trim(\preg_replace('/[\s]+/', ' ', $sSearch));
            }

            foreach ($mMatch[1] as $iIndex => $sName) {
                if (isset($mMatch[2][$iIndex]) && 0 < \strlen($mMatch[2][$iIndex])) {
                    $sName = \strtoupper($sName);
                    $sValue = $mMatch[2][$iIndex];
                    switch ($sName) {
                        case 'TEXT':
                        case 'BODY':
                        case 'EMAIL':
                        case 'MAIL':
                        case 'FROM':
                        case 'TO':
                        case 'SUBJECT':
                        case 'IS':
                        case 'HAS':
                        case 'SIZE':
                        case 'SMALLER':
                        case 'LARGER':
                        case 'BIGGER':
                        case 'MAXSIZE':
                        case 'MINSIZE':
                        case 'DATE':
                            if ('MAIL' === $sName) {
                                $sName = 'EMAIL';
                            }
                            if ('BODY' === $sName) {
                                $sName = 'TEXT';
                            }
                            if ('SIZE' === $sName || 'BIGGER' === $sName || 'MINSIZE' === $sName) {
                                $sName = 'LARGER';
                            }
                            if ('MAXSIZE' === $sName) {
                                $sName = 'SMALLER';
                            }
                            $aResult[$sName] = $sValue;
                            break;
                    }
                }
            }
        }

        $aResult['OTHER'] = $sSearch;
        foreach ($aResult as $sName => $sValue) {
            if (isset($aCache[$sValue])) {
                $aResult[$sName] = \trim($aCache[$sValue], '"\' ');
            }
        }

        return $aResult;
    }

    /**
     * @param string $sSearch
     * @param int $iTimeZoneOffset = 0
     * @param bool $bUseCache = true
     *
     * @return string
     */
    private function getImapSearchCriterias($sSearch, $iTimeZoneOffset = 0, &$bUseCache = true)
    {
        $bUseCache = true;
        $iTimeFilter = 0;
        $aCriteriasResult = array();

        if (0 < \MailSo\Config::$MessageListDateFilter) {
            $iD = \time() - 3600 * 24 * 30 * \MailSo\Config::$MessageListDateFilter;
            $iTimeFilter = \gmmktime(1, 1, 1, \gmdate('n', $iD), 1, \gmdate('Y', $iD));
        }

        if (0 < \strlen(\trim($sSearch))) {
            $sGmailRawSearch = '';
            $sResultBodyTextSearch = '';

            $aLines = $this->parseSearchString($sSearch);
            $bIsGmail = $this->oImapClient->IsSupported('X-GM-EXT-1');

            if (1 === \count($aLines) && isset($aLines['OTHER'])) {
                $sValue = $this->escapeSearchString($aLines['OTHER']);

                if (\MailSo\Config::$MessageListFastSimpleSearch) {
                    $aCriteriasResult[] = 'OR OR OR';
                    $aCriteriasResult[] = 'FROM';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'TO';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'CC';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'SUBJECT';
                    $aCriteriasResult[] = $sValue;
                } else {
                    $aCriteriasResult[] = 'TEXT';
                    $aCriteriasResult[] = $sValue;
                }
            } else {
                if (isset($aLines['EMAIL'])) {
                    $sValue = $this->escapeSearchString($aLines['EMAIL']);

                    $aCriteriasResult[] = 'OR OR';
                    $aCriteriasResult[] = 'FROM';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'TO';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'CC';
                    $aCriteriasResult[] = $sValue;

                    unset($aLines['EMAIL']);
                }

                if (isset($aLines['TO'])) {
                    $sValue = $this->escapeSearchString($aLines['TO']);

                    $aCriteriasResult[] = 'OR';
                    $aCriteriasResult[] = 'TO';
                    $aCriteriasResult[] = $sValue;
                    $aCriteriasResult[] = 'CC';
                    $aCriteriasResult[] = $sValue;

                    unset($aLines['TO']);
                }

                $sMainText = '';
                foreach ($aLines as $sName => $sRawValue) {
                    if ('' === \trim($sRawValue)) {
                        continue;
                    }

                    $sValue = $this->escapeSearchString($sRawValue);
                    switch ($sName) {
                        case 'FROM':
                            $aCriteriasResult[] = 'FROM';
                            $aCriteriasResult[] = $sValue;
                            break;
                        case 'SUBJECT':
                            $aCriteriasResult[] = 'SUBJECT';
                            $aCriteriasResult[] = $sValue;
                            break;
                        case 'OTHER':
                        case 'TEXT':
                            $sMainText .= ' '.$sRawValue;
                            break;
                        case 'HAS':
                            $aValue = \explode(',', \strtolower($sRawValue));
                            $aValue = \array_map('trim', $aValue);

                            $aCompareArray = array('file', 'files', 'attach', 'attachs', 'attachment', 'attachments');
                            if (\count($aCompareArray) > \count(\array_diff($aCompareArray, $aValue))) {
                                if ($bIsGmail) {
                                    $sGmailRawSearch .= ' has:attachment';
                                } else {
                                    // Simple, is not detailed search (Sometimes doesn't work)
                                    $aCriteriasResult[] = 'OR OR OR';
                                    $aCriteriasResult[] = 'HEADER Content-Type application/';
                                    $aCriteriasResult[] = 'HEADER Content-Type multipart/m';
                                    $aCriteriasResult[] = 'HEADER Content-Type multipart/signed';
                                    $aCriteriasResult[] = 'HEADER Content-Type multipart/report';
                                }
                            }

                            // no break
                        case 'IS':
                            $aValue = \explode(',', \strtolower($sRawValue));
                            $aValue = \array_map('trim', $aValue);

                            $aCompareArray = array('flag', 'flagged', 'star', 'starred', 'pinned');
                            $aCompareArray2 = array('unflag', 'unflagged', 'unstar', 'unstarred', 'unpinned');
                            if (\count($aCompareArray) > \count(\array_diff($aCompareArray, $aValue))) {
                                $aCriteriasResult[] = 'FLAGGED';
                                $bUseCache = false;
                            } elseif (\count($aCompareArray2) > \count(\array_diff($aCompareArray2, $aValue))) {
                                $aCriteriasResult[] = 'UNFLAGGED';
                                $bUseCache = false;
                            }

                            $aCompareArray = array('unread', 'unseen');
                            $aCompareArray2 = array('read', 'seen');
                            if (\count($aCompareArray) > \count(\array_diff($aCompareArray, $aValue))) {
                                $aCriteriasResult[] = 'UNSEEN';
                                $bUseCache = false;
                            } elseif (\count($aCompareArray2) > \count(\array_diff($aCompareArray2, $aValue))) {
                                $aCriteriasResult[] = 'SEEN';
                                $bUseCache = false;
                            }
                            break;

                        case 'LARGER':
                            $aCriteriasResult[] = 'LARGER';
                            $aCriteriasResult[] =  $this->parseFriendlySize($sRawValue);
                            break;
                        case 'SMALLER':
                            $aCriteriasResult[] = 'SMALLER';
                            $aCriteriasResult[] =  $this->parseFriendlySize($sRawValue);
                            break;
                        case 'DATE':
                            $iDateStampFrom = $iDateStampTo = 0;

                            $sDate = $sRawValue;
                            $aDate = \explode('/', $sDate);

                            if (\is_array($aDate) && 2 === \count($aDate)) {
                                if (0 < \strlen($aDate[0])) {
                                    $iDateStampFrom = $this->parseSearchDate($aDate[0], $iTimeZoneOffset);
                                }

                                if (0 < \strlen($aDate[1])) {
                                    $iDateStampTo = $this->parseSearchDate($aDate[1], $iTimeZoneOffset);
                                    $iDateStampTo += 60 * 60 * 24;
                                }
                            } else {
                                if (0 < \strlen($sDate)) {
                                    $iDateStampFrom = $this->parseSearchDate($sDate, $iTimeZoneOffset);
                                    $iDateStampTo = $iDateStampFrom + 60 * 60 * 24;
                                }
                            }

                            if (0 < $iDateStampFrom) {
                                $aCriteriasResult[] = 'SINCE';
                                $aCriteriasResult[] = \gmdate('j-M-Y', $iTimeFilter > $iDateStampFrom ?
                                    $iDateStampFrom : $iTimeFilter);

                                if (0 < $iTimeFilter && $iTimeFilter > $iDateStampFrom) {
                                    $iTimeFilter = 0;
                                }
                            }

                            if (0 < $iDateStampTo) {
                                $aCriteriasResult[] = 'BEFORE';
                                $aCriteriasResult[] = \gmdate('j-M-Y', $iDateStampTo);
                            }
                            break;
                    }
                }

                if ('' !== \trim($sMainText)) {
                    $sMainText = \trim(\trim(preg_replace('/[\s]+/', ' ', $sMainText)), '"');
                    if ($bIsGmail) {
                        $sGmailRawSearch .= ' '.$sMainText;
                    } else {
                        $sResultBodyTextSearch .= ' '.$sMainText;
                    }
                }
            }

            $sGmailRawSearch = \trim($sGmailRawSearch);
            if ($bIsGmail && 0 < \strlen($sGmailRawSearch)) {
                $aCriteriasResult[] = 'X-GM-RAW';
                $aCriteriasResult[] = $this->escapeSearchString($sGmailRawSearch, false);
            }

            $sResultBodyTextSearch = \trim($sResultBodyTextSearch);
            if (0 < \strlen($sResultBodyTextSearch)) {
                $aCriteriasResult[] = 'BODY';
                $aCriteriasResult[] = $this->escapeSearchString($sResultBodyTextSearch);
            }
        }

        $sCriteriasResult = \trim(\implode(' ', $aCriteriasResult));

        if (0 < $iTimeFilter) {
            $sCriteriasResult .= ' SINCE '.\gmdate('j-M-Y', $iTimeFilter);
        }

        $sCriteriasResult = \trim($sCriteriasResult);
        if ('' === $sCriteriasResult) {
            $sCriteriasResult = 'ALL';
        }

        return $sCriteriasResult;
    }

    /**
     * @param array $aThreads
     * @return array
     */
    private function threadArrayMap($aThreads)
    {
        $aNew = array();
        foreach ($aThreads as $mItem) {
            if (!\is_array($mItem)) {
                $aNew[] = $mItem;
            } else {
                $mMap = $this->threadArrayMap($mItem);
                if (\is_array($mMap) && 0 < \count($mMap)) {
                    $aNew = \array_merge($aNew, $mMap);
                }
            }
        }

        return $aNew;
    }

    /**
     * @param array $aThreads
     *
     * @return array
     */
    private function compileThreadArray($aThreads)
    {
        $aResult = array();
        foreach ($aThreads as $mItem) {
            if (\is_array($mItem)) {
                $aMap = $this->threadArrayMap($mItem);
                if (\is_array($aMap)) {
                    if (1 < \count($aMap)) {
                        $aResult[] = $aMap;
                    } elseif (0 < \count($aMap)) {
                        $aResult[] = $aMap[0];
                    }
                }
            } else {
                $aResult[] = $mItem;
            }
        }

        return $aResult;
    }

    /**
     * @param string $sFolderName
     * @param string $sFolderHash
     * @param array $aIndexOrUids
     * @param \MailSo\Cache\CacheClient $oCacher
     * @param int $iThreadLimit = 100
     *
     * @return array
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageListThreadsMap($sFolderName, $sFolderHash, $aIndexOrUids, $oCacher, $iThreadLimit = 100)
    {
        $iThreadLimit = \is_int($iThreadLimit) && 0 < $iThreadLimit ? $iThreadLimit : 0;

        $sSearchHash = '';
        if (0 < \MailSo\Config::$MessageListDateFilter) {
            $iD = \time() - 3600 * 24 * 30 * \MailSo\Config::$MessageListDateFilter;
            $iTimeFilter = \gmmktime(1, 1, 1, \gmdate('n', $iD), 1, \gmdate('Y', $iD));

            $sSearchHash .= ' SINCE '.\gmdate('j-M-Y', $iTimeFilter);
        }

        if ('' === \trim($sSearchHash)) {
            $sSearchHash = 'ALL';
        }

        if ($oCacher && $oCacher->IsInited()) {
            $sSerializedHash =
                'ThreadsMapSorted/'.$sSearchHash.'/'.
                'Limit='.$iThreadLimit.'/'.
                $this->oImapClient->GetLogginedUser().'@'.
                $this->oImapClient->GetConnectedHost().':'.
                $this->oImapClient->GetConnectedPort().'/'.
                $sFolderName.'/'.$sFolderHash;

            $sSerializedUids = $oCacher->get($sSerializedHash);
            if (!empty($sSerializedUids)) {
                $aSerializedUids = @\unserialize($sSerializedUids);
                if (is_array($aSerializedUids)) {
                    if ($this->oLogger) {
                        $this->oLogger->Write('Get Serialized Thread UIDS from cache ("'.$sFolderName.'" / '.$sSearchHash.') [count:'.\count($aSerializedUids).']');
                    }

                    return $aSerializedUids;
                }
            }
        }

        $this->oImapClient->FolderExamine($sFolderName);

        $aThreadUids = array();
        try {
            $aThreadUids = $this->oImapClient->MessageSimpleThread($sSearchHash);
        } catch (\MailSo\Imap\Exceptions\RuntimeException $oException) {
            $aThreadUids = array();
        }

        $aResult = array();
        $aCompiledThreads = $this->compileThreadArray($aThreadUids);

        foreach ($aCompiledThreads as $mData) {
            if (\is_array($mData)) {
                foreach ($mData as $mSubData) {
                    $aResult[(int) $mSubData] =
                        \array_diff($mData, array((int) $mSubData));
                }
            } elseif (\is_int($mData) || \is_string($mData)) {
                $aResult[(int) $mData] = (int) $mData;
            }
        }

        $aParentsMap = array();
        foreach ($aIndexOrUids as $iUid) {
            if (isset($aResult[$iUid]) && \is_array($aResult[$iUid])) {
                foreach ($aResult[$iUid] as $iTempUid) {
                    $aParentsMap[$iTempUid] = $iUid;
                    if (isset($aResult[$iTempUid])) {
                        unset($aResult[$iTempUid]);
                    }
                }
            }
        }

        $aSortedThreads = array();
        foreach ($aIndexOrUids as $iUid) {
            if (isset($aResult[$iUid])) {
                $aSortedThreads[$iUid] = $iUid;
            }
        }

        foreach ($aIndexOrUids as $iUid) {
            if (!isset($aSortedThreads[$iUid]) &&
                isset($aParentsMap[$iUid]) &&
                isset($aSortedThreads[$aParentsMap[$iUid]])) {
                if (!\is_array($aSortedThreads[$aParentsMap[$iUid]])) {
                    $aSortedThreads[$aParentsMap[$iUid]] = array();
                }

                $aSortedThreads[$aParentsMap[$iUid]][] = $iUid;
            }
        }

        $aResult = $aSortedThreads;
        unset($aParentsMap, $aSortedThreads);

        $aTemp = array();
        foreach ($aResult as $iUid => $mValue) {
            if (0 < $iThreadLimit && \is_array($mValue) && $iThreadLimit < \count($mValue)) {
                $aParts = \array_chunk($mValue, $iThreadLimit);
                if (0 < count($aParts)) {
                    foreach ($aParts as $iIndex => $aItem) {
                        if (0 === $iIndex) {
                            $aResult[$iUid] = $aItem;
                        } elseif (0 < $iIndex && \is_array($aItem)) {
                            $mFirst = \array_shift($aItem);
                            if (!empty($mFirst)) {
                                $aTemp[$mFirst] = 0 < \count($aItem) ? $aItem : $mFirst;
                            }
                        }
                    }
                }
            }
        }

        foreach ($aTemp as $iUid => $mValue) {
            $aResult[$iUid] = $mValue;
        }

        unset($aTemp);

        $aLastResult = array();
        foreach ($aIndexOrUids as $iUid) {
            if (isset($aResult[$iUid])) {
                $aLastResult[$iUid] = $aResult[$iUid];
            }
        }

        $aResult = $aLastResult;
        unset($aLastResult);

        if ($oCacher && $oCacher->IsInited() && !empty($sSerializedHash)) {
            $oCacher->Set($sSerializedHash, serialize($aResult));

            if ($this->oLogger) {
                $this->oLogger->Write('Save Serialized Thread UIDS to cache ("'.$sFolderName.'" / '.$sSearchHash.') [count:'.\count($aResult).']');
            }
        }

        return $aResult;
    }

    /**
     * @param \MailSo\Mail\MessageCollection &$oMessageCollection
     * @param array $aRequestIndexOrUids
     * @param bool $bIndexAsUid
     *
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageListByRequestIndexOrUids(&$oMessageCollection, $aRequestIndexOrUids, $bIndexAsUid)
    {
        if (\is_array($aRequestIndexOrUids) && 0 < \count($aRequestIndexOrUids)) {
            $aFetchResponse = $this->oImapClient->Fetch(array(
                \MailSo\Imap\Enumerations\FetchType::INDEX,
                \MailSo\Imap\Enumerations\FetchType::UID,
                \MailSo\Imap\Enumerations\FetchType::RFC822_SIZE,
                \MailSo\Imap\Enumerations\FetchType::INTERNALDATE,
                \MailSo\Imap\Enumerations\FetchType::FLAGS,
                \MailSo\Imap\Enumerations\FetchType::BODYSTRUCTURE,
                $this->getEnvelopeOrHeadersRequestString()
            ), \MailSo\Base\Utils::PrepearFetchSequence($aRequestIndexOrUids), $bIndexAsUid);

            if (\is_array($aFetchResponse) && 0 < \count($aFetchResponse)) {
                $aFetchIndexArray = array();
                $oFetchResponseItem = null;
                foreach ($aFetchResponse as /* @var $oFetchResponseItem \MailSo\Imap\FetchResponse */ &$oFetchResponseItem) {
                    $aFetchIndexArray[($bIndexAsUid)
                        ? $oFetchResponseItem->GetFetchValue(\MailSo\Imap\Enumerations\FetchType::UID)
                        : $oFetchResponseItem->GetFetchValue(\MailSo\Imap\Enumerations\FetchType::INDEX)] =& $oFetchResponseItem;

                    unset($oFetchResponseItem);
                }

                foreach ($aRequestIndexOrUids as $iFUid) {
                    if (isset($aFetchIndexArray[$iFUid])) {
                        $oMessageCollection->Add(
                            Message::NewFetchResponseInstance(
                                $oMessageCollection->FolderName,
                                $aFetchIndexArray[$iFUid]
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * @return bool
     *
     * @throws \MailSo\Net\Exceptions\Exception
     */
    public function IsThreadsSupported()
    {
        return $this->oImapClient->IsSupported('THREAD=REFS') ||
            $this->oImapClient->IsSupported('THREAD=REFERENCES') ||
            $this->oImapClient->IsSupported('THREAD=ORDEREDSUBJECT');
    }

    /**
     * @param string $sSearch
     * @param string $sFolderName
     * @param string|bool $sFolderHash
     * @param bool $bUseSortIfSupported = true
     * @param bool $bUseESearchOrESortRequest = false
     * @param \MailSo\Cache\CacheClient|null $oCacher = null
     *
     * @return Array|bool
     */
    private function getSearchUidsResult(
        $sSearch,
        $sFolderName,
        $sFolderHash,
        $bUseSortIfSupported = true,
        $bUseESearchOrESortRequest = false,
        $oCacher = null
    ) {
        $bUidsFromCacher = false;
        $aResultUids = false;
        $bUseCacheAfterSearch = true;
        $sSerializedHash = '';
        $sSerializedLog = '';

        $bESortSupported = $bUseSortIfSupported && $bUseESearchOrESortRequest ? $this->oImapClient->IsSupported('ESORT') : false;
        $bESearchSupported = $bUseESearchOrESortRequest ? $this->oImapClient->IsSupported('ESEARCH') : false;
        $bUseSortIfSupported = $bUseSortIfSupported ? $this->oImapClient->IsSupported('SORT') : false;

        $sSearchCriterias = $this->getImapSearchCriterias($sSearch, 0, $bUseCacheAfterSearch);
        if ($bUseCacheAfterSearch && $oCacher && $oCacher->IsInited()) {
            $sSerializedHash =
                ($bUseSortIfSupported ? 'S' : 'N').'/'.
                $this->oImapClient->GetLogginedUser().'@'.
                $this->oImapClient->GetConnectedHost().':'.
                $this->oImapClient->GetConnectedPort().'/'.
                $sFolderName.'/'.
                $sSearchCriterias;

            $sSerializedLog = '"'.$sFolderName.'" / '.$sSearchCriterias.'';

            $sSerialized = $oCacher->get($sSerializedHash);
            if (!empty($sSerialized)) {
                $aSerialized = @\unserialize($sSerialized);
                if (\is_array($aSerialized) && isset($aSerialized['FolderHash'], $aSerialized['Uids']) &&
                    \is_array($aSerialized['Uids']) &&
                    ($sFolderHash === $aSerialized['FolderHash'])) {
                    if ($this->oLogger) {
                        $this->oLogger->Write('Get Serialized UIDS from cache ('.$sSerializedLog.') [count:'.\count($aSerialized['Uids']).']');
                    }

                    $aResultUids = $aSerialized['Uids'];
                    $bUidsFromCacher = true;
                }
            }
        }

        if (!\is_array($aResultUids)) {
            if ($bUseSortIfSupported) {
                if ($bESortSupported) {
                    $aESorthData = $this->oImapClient->MessageSimpleESort(array('ARRIVAL'), $sSearchCriterias, array('ALL'), true, '');
                    if (isset($aESorthData['ALL'])) {
                        $aResultUids = \MailSo\Base\Utils::ParseFetchSequence($aESorthData['ALL']);
                        $aResultUids = \array_reverse($aResultUids);
                    }

                    unset($aESorthData);
                } else {
                    $aResultUids = $this->oImapClient->MessageSimpleSort(array('REVERSE ARRIVAL'), $sSearchCriterias, true);
                }
            } else {
                if (!\MailSo\Base\Utils::IsAscii($sSearch)) {
                    try {
                        if ($bESearchSupported) {
                            $aESearchData = $this->oImapClient->MessageSimpleESearch($sSearchCriterias, array('ALL'), true, '', 'UTF-8');
                            if (isset($aESearchData['ALL'])) {
                                $aResultUids = \MailSo\Base\Utils::ParseFetchSequence($aESearchData['ALL']);
                                $aResultUids = \array_reverse($aResultUids);
                            }
                            unset($aESearchData);
                        } else {
                            $aResultUids = $this->oImapClient->MessageSimpleSearch($sSearchCriterias, true, 'UTF-8');
                        }
                    } catch (\MailSo\Imap\Exceptions\NegativeResponseException $oException) {
                        $oException = null;
                        $aResultUids = false;
                    }
                }

                if (false === $aResultUids) {
                    if ($bESearchSupported) {
                        $aESearchData = $this->oImapClient->MessageSimpleESearch($sSearchCriterias, array('ALL'), true);
                        if (isset($aESearchData['ALL'])) {
                            $aResultUids = \MailSo\Base\Utils::ParseFetchSequence($aESearchData['ALL']);
                            $aResultUids = \array_reverse($aResultUids);
                        }

                        unset($aESearchData);
                    } else {
                        $aResultUids = $this->oImapClient->MessageSimpleSearch($sSearchCriterias, true);
                    }
                }
            }

            if (!$bUidsFromCacher && $bUseCacheAfterSearch && \is_array($aResultUids) && $oCacher && $oCacher->IsInited() && 0 < \strlen($sSerializedHash)) {
                $oCacher->Set($sSerializedHash, \serialize(array(
                    'FolderHash' => $sFolderHash,
                    'Uids' => $aResultUids
                )));

                if ($this->oLogger) {
                    $this->oLogger->Write('Save Serialized UIDS to cache ('.$sSerializedLog.') [count:'.\count($aResultUids).']');
                }
            }
        }

        return $aResultUids;
    }

    /**
     * @param string $sFolderName
     * @param int $iOffset = 0
     * @param int $iLimit = 10
     * @param string $sSearch = ''
     * @param string $sPrevUidNext = ''
     * @param \MailSo\Cache\CacheClient|null $oCacher = null
     * @param bool $bUseSortIfSupported = false
     * @param bool $bUseThreadSortIfSupported = false
     * @param array $aExpandedThreadsUids = array()
     * @param bool $bUseESearchOrESortRequest = false
     *
     * @return \MailSo\Mail\MessageCollection
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Net\Exceptions\Exception
     * @throws \MailSo\Imap\Exceptions\Exception
     */
    public function MessageList(
        $sFolderName,
        $iOffset = 0,
        $iLimit = 10,
        $sSearch = '',
        $sPrevUidNext = '',
        $oCacher = null,
        $bUseSortIfSupported = false,
        $bUseThreadSortIfSupported = false,
        $aExpandedThreadsUids = array(),
        $bUseESearchOrESortRequest = false
    ) {
        $sSearch = \trim($sSearch);
        if (!\MailSo\Base\Validator::RangeInt($iOffset, 0) ||
            !\MailSo\Base\Validator::RangeInt($iLimit, 0, 999)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderExamine($sFolderName);

        $oMessageCollection = MessageCollection::NewInstance();
        $oMessageCollection->FolderName = $sFolderName;
        $oMessageCollection->Offset = $iOffset;
        $oMessageCollection->Limit = $iLimit;
        $oMessageCollection->Search = $sSearch;

        $aLastCollapsedThreadUids = array();

        $aThreads = array();

        $iMessageCount = 0;
        $iMessageRealCount = 0;
        $iMessageUnseenCount = 0;
        $sUidNext = '0';

        $bUseSortIfSupported = $bUseSortIfSupported ? $this->oImapClient->IsSupported('SORT') : false;

        $bUseThreadSortIfSupported = $bUseThreadSortIfSupported ?
            ($this->oImapClient->IsSupported('THREAD=REFS') || $this->oImapClient->IsSupported('THREAD=REFERENCES') || $this->oImapClient->IsSupported('THREAD=ORDEREDSUBJECT')) : false;

        if (!$oCacher || !($oCacher instanceof \MailSo\Cache\CacheClient)) {
            $oCacher = null;
        }

        $this->initFolderValues($sFolderName, $iMessageRealCount, $iMessageUnseenCount, $sUidNext);
        $iMessageCount = $iMessageRealCount;

        $oMessageCollection->FolderHash = self::GenerateHash($sFolderName, $iMessageRealCount, $iMessageUnseenCount, $sUidNext);
        $oMessageCollection->UidNext = $sUidNext;
        $oMessageCollection->NewMessages = $this->getFolderNextMessageInformation($sFolderName, $sPrevUidNext, $sUidNext);

        $bSearch = false;
        $bMessageListOptimization = 0 < \MailSo\Config::$MessageListCountLimitTrigger &&
            \MailSo\Config::$MessageListCountLimitTrigger < $iMessageRealCount;

        if ($bMessageListOptimization) {
            $bUseSortIfSupported = false;
            $bUseThreadSortIfSupported = false;
            $bUseESearchOrESortRequest = false;
        }

        if (0 < $iMessageRealCount) {
            $bIndexAsUid = false;
            $aIndexOrUids = array();

            if (0 < \strlen($sSearch)) {
                $aIndexOrUids = $this->getSearchUidsResult(
                    $sSearch,
                    $oMessageCollection->FolderName,
                    $oMessageCollection->FolderHash,
                    $bUseSortIfSupported,
                    $bUseESearchOrESortRequest,
                    $oCacher
                );

                $bIndexAsUid = true;
            } else {
                if ($bUseThreadSortIfSupported && 1 < $iMessageCount) {
                    $aIndexOrUids = $this->getSearchUidsResult(
                        '',
                        $oMessageCollection->FolderName,
                        $oMessageCollection->FolderHash,
                        $bUseSortIfSupported,
                        $bUseESearchOrESortRequest,
                        $oCacher
                    );

                    $aThreads = $this->MessageListThreadsMap(
                        $oMessageCollection->FolderName,
                        $oMessageCollection->FolderHash,
                        $aIndexOrUids,
                        $oCacher,
                        \MailSo\Config::$LargeThreadLimit
                    );

                    $aExpandedThreadsUids = \is_array($aExpandedThreadsUids) ? $aExpandedThreadsUids : array();
                    $bWatchExpanded = 0 < \count($aExpandedThreadsUids);

                    $aNewIndexOrUids = array();
                    foreach ($aIndexOrUids as $iUid) {
                        if (isset($aThreads[$iUid])) {
                            $aNewIndexOrUids[] = $iUid;
                            if (\is_array($aThreads[$iUid])) {
                                if ($bWatchExpanded && \in_array($iUid, $aExpandedThreadsUids)) {
                                    $aSubArray = $aThreads[$iUid];
                                    foreach ($aSubArray as $iSubRootUid) {
                                        $aNewIndexOrUids[] = $iSubRootUid;
                                    }
                                } else {
                                    $aLastCollapsedThreadUids[] = $iUid;
                                }
                            }
                        }
                    }

                    $aIndexOrUids = $aNewIndexOrUids;
                    unset($aNewIndexOrUids);

                    $iMessageCount = \count($aIndexOrUids);
                    $bIndexAsUid = true;
                } else {
                    $aIndexOrUids = array(1);
                    $bIndexAsUid = false;

                    if (1 < $iMessageCount) {
                        if ($bMessageListOptimization || 0 === \MailSo\Config::$MessageListDateFilter) {
                            $aIndexOrUids = \array_reverse(\range(1, $iMessageCount));
                        } else {
                            $aIndexOrUids = $this->getSearchUidsResult(
                                '',
                                $oMessageCollection->FolderName,
                                $oMessageCollection->FolderHash,
                                $bUseSortIfSupported,
                                $bUseESearchOrESortRequest,
                                $oCacher
                            );

                            $bIndexAsUid = true;
                        }
                    }
                }
            }

            if (\is_array($aIndexOrUids)) {
                $oMessageCollection->MessageCount = $iMessageRealCount;
                $oMessageCollection->MessageUnseenCount = $iMessageUnseenCount;
                $oMessageCollection->MessageResultCount = 0 === \strlen($sSearch)
                    ? $iMessageCount : \count($aIndexOrUids);

                if (0 < \count($aIndexOrUids)) {
                    $iOffset = (0 > $iOffset) ? 0 : $iOffset;
                    $aRequestIndexOrUids = \array_slice($aIndexOrUids, $iOffset, $iLimit);

                    $this->MessageListByRequestIndexOrUids($oMessageCollection, $aRequestIndexOrUids, $bIndexAsUid);
                }
            }
        }

        $aLastCollapsedThreadUidsForPage = array();

        if (!$bSearch && $bUseThreadSortIfSupported && 0 < \count($aThreads)) {
            $oMessageCollection->ForeachList(function (/* @var $oMessage \MailSo\Mail\Message */ $oMessage) use (
                $aThreads,
                $aLastCollapsedThreadUids,
                &$aLastCollapsedThreadUidsForPage
            ) {
                $iUid = $oMessage->Uid();
                if (\in_array($iUid, $aLastCollapsedThreadUids)) {
                    $aLastCollapsedThreadUidsForPage[] = $iUid;
                }

                if (isset($aThreads[$iUid]) && \is_array($aThreads[$iUid]) && 0 < \count($aThreads[$iUid])) {
                    $oMessage->SetThreads($aThreads[$iUid]);
                    $oMessage->SetThreadsLen(\count($aThreads[$iUid]));
                } elseif (!isset($aThreads[$iUid])) {
                    foreach ($aThreads as $iKeyUid => $mSubItem) {
                        if (\is_array($mSubItem) && \in_array($iUid, $mSubItem)) {
                            $oMessage->SetParentThread($iKeyUid);
                            $oMessage->SetThreadsLen(\count($mSubItem));
                        }
                    }
                }
            });

            $oMessageCollection->LastCollapsedThreadUids = $aLastCollapsedThreadUidsForPage;
        }

        return $oMessageCollection;
    }

    /**
     * @return array|false
     */
    public function Quota()
    {
        return $this->oImapClient->Quota();
    }

    /**
     * @param string $sFolderName
     * @param string $sMessageId
     *
     * @return int|null
     */
    public function FindMessageUidByMessageId($sFolderName, $sMessageId)
    {
        if (0 === \strlen($sMessageId)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderExamine($sFolderName);

        $aUids = $this->oImapClient->MessageSimpleSearch(
            'HEADER Message-ID '.$sMessageId,
            true
        );

        return \is_array($aUids) && 1 === \count($aUids) && \is_numeric($aUids[0]) ? (int) $aUids[0] : null;
    }

    /**
     * @param string $sParent = ''
     * @param string $sListPattern = '*'
     * @param bool $bUseListSubscribeStatus = false
     *
     * @return \MailSo\Mail\FolderCollection|false
     */
    public function Folders($sParent = '', $sListPattern = '*', $bUseListSubscribeStatus = true)
    {
        $oFolderCollection = false;

        $aFolders = $this->oImapClient->FolderList($sParent, $sListPattern);

        $aSubscribedFolders = null;
        if ($bUseListSubscribeStatus) {
            try {
                $aSubscribedFolders = $this->oImapClient->FolderSubscribeList($sParent, $sListPattern);
            } catch (\Exception $oException) {
            }
        }

        $aImapSubscribedFoldersHelper = null;
        if (\is_array($aSubscribedFolders)) {
            $aImapSubscribedFoldersHelper = array();
            foreach ($aSubscribedFolders as /* @var $oImapFolder \MailSo\Imap\Folder */ $oImapFolder) {
                $aImapSubscribedFoldersHelper[] = $oImapFolder->FullNameRaw();
            }
        }

        $aMailFoldersHelper = null;
        if (\is_array($aFolders)) {
            $aMailFoldersHelper = array();

            foreach ($aFolders as /* @var $oImapFolder \MailSo\Imap\Folder */ $oImapFolder) {
                $aMailFoldersHelper[] = Folder::NewInstance(
                    $oImapFolder,
                    (null === $aImapSubscribedFoldersHelper || \in_array($oImapFolder->FullNameRaw(), $aImapSubscribedFoldersHelper)) ||
                    $oImapFolder->IsInbox()
                );
            }
        }

        if (\is_array($aMailFoldersHelper)) {
            $oFolderCollection = FolderCollection::NewInstance();
            $oFolderCollection->InitByUnsortedMailFolderArray($aMailFoldersHelper);
        }

        if ($oFolderCollection) {
            $oFolderCollection->SortByCallback(function ($oFolderA, $oFolderB) {
                $sA = \strtoupper($oFolderA->FullNameRaw());
                $sB = \strtoupper($oFolderB->FullNameRaw());
                switch (true) {
                    case 'INBOX' === $sA:
                        return -1;
                    case 'INBOX' === $sB:
                        return 1;
                    case '[GMAIL]' === $sA:
                        return -1;
                    case '[GMAIL]' === $sB:
                        return 1;
                }

                return \strnatcasecmp($oFolderA->FullName(), $oFolderB->FullName());
            });

            $oNamespace = $this->oImapClient->GetNamespace();
            if ($oNamespace) {
                $oFolderCollection->SetNamespace($oNamespace->GetPersonalNamespace());
            }

            $oFolderCollection->IsThreadsSupported = $this->IsThreadsSupported();
        }


        return $oFolderCollection;
    }

    /**
     * @param string $sFolderNameInUtf
     * @param string $sFolderParentFullNameRaw = ''
     * @param bool $bSubscribeOnCreation = true
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function FolderCreate($sFolderNameInUtf, $sFolderParentFullNameRaw = '', $bSubscribeOnCreation = true)
    {
        if (!\MailSo\Base\Validator::NotEmptyString($sFolderNameInUtf, true) ||
            !\is_string($sFolderParentFullNameRaw)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $sFolderNameInUtf = trim($sFolderNameInUtf);

        $aFolders = $this->oImapClient->FolderList('', 0 === \strlen(\trim($sFolderParentFullNameRaw)) ? 'INBOX' : $sFolderParentFullNameRaw);
        if (!\is_array($aFolders) || !isset($aFolders[0])) {
            // TODO
            throw new \MailSo\Mail\Exceptions\RuntimeException(
                0 === \strlen(trim($sFolderParentFullNameRaw))
                    ? 'Cannot get folder delimiter'
                    : 'Cannot create folder in non-existen parent folder'
            );
        }

        $sDelimiter = $aFolders[0]->Delimiter();
        if (0 < \strlen($sDelimiter) && 0 < \strlen(\trim($sFolderParentFullNameRaw))) {
            $sFolderParentFullNameRaw .= $sDelimiter;
        }

        $sFullNameRawToCreate = \MailSo\Base\Utils::ConvertEncoding(
            $sFolderNameInUtf,
            \MailSo\Base\Enumerations\Charset::UTF_8,
            \MailSo\Base\Enumerations\Charset::UTF_7_IMAP
        );

        if (0 < \strlen($sDelimiter) && false !== \strpos($sFullNameRawToCreate, $sDelimiter)) {
            // TODO
            throw new \MailSo\Mail\Exceptions\RuntimeException(
                'New folder name contains delimiter'
            );
        }

        $sFullNameRawToCreate = $sFolderParentFullNameRaw.$sFullNameRawToCreate;

        $this->oImapClient->FolderCreate($sFullNameRawToCreate);

        if ($bSubscribeOnCreation) {
            $this->oImapClient->FolderSubscribe($sFullNameRawToCreate);
        }

        return $this;
    }

    /**
     * @param string $sPrevFolderFullNameRaw
     * @param string $sNextFolderFullNameInUtf
     * @param bool $bSubscribeOnRename = true
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function FolderMove($sPrevFolderFullNameRaw, $sNextFolderFullNameInUtf, $bSubscribeOnRename = true)
    {
        return $this->folderModify($sPrevFolderFullNameRaw, $sNextFolderFullNameInUtf, false, $bSubscribeOnRename);
    }

    /**
     * @param string $sPrevFolderFullNameRaw
     * @param string $sNewTopFolderNameInUtf
     * @param bool $bSubscribeOnRename = true
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function FolderRename($sPrevFolderFullNameRaw, $sNewTopFolderNameInUtf, $bSubscribeOnRename = true)
    {
        return $this->folderModify($sPrevFolderFullNameRaw, $sNewTopFolderNameInUtf, true, $bSubscribeOnRename);
    }

    /**
     * @param string $sPrevFolderFullNameRaw
     * @param string $sNextFolderNameInUtf
     * @param bool $bRenameOrMove
     * @param bool $bSubscribeOnModify
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function folderModify($sPrevFolderFullNameRaw, $sNextFolderNameInUtf, $bRenameOrMove, $bSubscribeOnModify)
    {
        if (0 === \strlen($sPrevFolderFullNameRaw) || 0 === \strlen($sNextFolderNameInUtf)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $aFolders = $this->oImapClient->FolderList('', $sPrevFolderFullNameRaw);
        if (!\is_array($aFolders) || !isset($aFolders[0])) {
            // TODO
            throw new \MailSo\Mail\Exceptions\RuntimeException('Cannot rename non-existen folder');
        }

        $sDelimiter = $aFolders[0]->Delimiter();
        $iLast = \strrpos($sPrevFolderFullNameRaw, $sDelimiter);

        $mSubscribeFolders = null;
        if ($bSubscribeOnModify) {
            $mSubscribeFolders = $this->oImapClient->FolderSubscribeList($sPrevFolderFullNameRaw, '*');
            if (\is_array($mSubscribeFolders) && 0 < count($mSubscribeFolders)) {
                foreach ($mSubscribeFolders as /* @var $oFolder \MailSo\Imap\Folder */ $oFolder) {
                    $this->oImapClient->FolderUnSubscribe($oFolder->FullNameRaw());
                }
            }
        }

        $sNewFolderFullNameRaw = \MailSo\Base\Utils::ConvertEncoding(
            $sNextFolderNameInUtf,
            \MailSo\Base\Enumerations\Charset::UTF_8,
            \MailSo\Base\Enumerations\Charset::UTF_7_IMAP
        );

        if ($bRenameOrMove) {
            if (0 < \strlen($sDelimiter) && false !== \strpos($sNewFolderFullNameRaw, $sDelimiter)) {
                // TODO
                throw new \MailSo\Mail\Exceptions\RuntimeException('New folder name contains delimiter');
            }

            $sFolderParentFullNameRaw = false === $iLast ? '' : \substr($sPrevFolderFullNameRaw, 0, $iLast + 1);
            $sNewFolderFullNameRaw = $sFolderParentFullNameRaw.$sNewFolderFullNameRaw;
        }

        $this->oImapClient->FolderRename($sPrevFolderFullNameRaw, $sNewFolderFullNameRaw);

        if (\is_array($mSubscribeFolders) && 0 < count($mSubscribeFolders)) {
            foreach ($mSubscribeFolders as /* @var $oFolder \MailSo\Imap\Folder */ $oFolder) {
                $sFolderFullNameRawForResubscrine = $oFolder->FullNameRaw();
                if (0 === \strpos($sFolderFullNameRawForResubscrine, $sPrevFolderFullNameRaw)) {
                    $sNewFolderFullNameRawForResubscrine = $sNewFolderFullNameRaw.
                        \substr($sFolderFullNameRawForResubscrine, \strlen($sPrevFolderFullNameRaw));

                    $this->oImapClient->FolderSubscribe($sNewFolderFullNameRawForResubscrine);
                }
            }
        }

        return $this;
    }

    /**
     * @param string $sFolderFullNameRaw
     * @param bool $bUnsubscribeOnDeletion = true
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     * @throws \MailSo\Mail\Exceptions\RuntimeException
     */
    public function FolderDelete($sFolderFullNameRaw, $bUnsubscribeOnDeletion = true)
    {
        if (0 === \strlen($sFolderFullNameRaw) || 'INBOX' === $sFolderFullNameRaw) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->FolderExamine($sFolderFullNameRaw);

        $aIndexOrUids = $this->oImapClient->MessageSimpleSearch('ALL');
        if (0 < \count($aIndexOrUids)) {
            throw new \MailSo\Mail\Exceptions\NonEmptyFolder();
        }

        $this->oImapClient->FolderExamine('INBOX');

        if ($bUnsubscribeOnDeletion) {
            $this->oImapClient->FolderUnSubscribe($sFolderFullNameRaw);
        }

        $this->oImapClient->FolderDelete($sFolderFullNameRaw);

        return $this;
    }

    /**
     * @param string $sFolderFullNameRaw
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function FolderClear($sFolderFullNameRaw)
    {
        $this->oImapClient->FolderSelect($sFolderFullNameRaw);

        $oFolderInformation = $this->oImapClient->FolderCurrentInformation();
        if ($oFolderInformation && 0 < $oFolderInformation->Exists) { // STATUS?
            $this->oImapClient->MessageStoreFlag(
                '1:*',
                false,
                array(\MailSo\Imap\Enumerations\MessageFlag::DELETED),
                \MailSo\Imap\Enumerations\StoreAction::ADD_FLAGS_SILENT
            );

            $this->oImapClient->MessageExpunge();
        }

        return $this;
    }

    /**
     * @param string $sFolderFullNameRaw
     * @param bool $bSubscribe
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function FolderSubscribe($sFolderFullNameRaw, $bSubscribe)
    {
        if (0 === \strlen($sFolderFullNameRaw)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oImapClient->{($bSubscribe) ? 'FolderSubscribe' : 'FolderUnSubscribe'}($sFolderFullNameRaw);

        return $this;
    }

    /**
     * @param \MailSo\Log\Logger $oLogger
     *
     * @return \MailSo\Mail\MailClient
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function SetLogger($oLogger)
    {
        if (!($oLogger instanceof \MailSo\Log\Logger)) {
            throw new \MailSo\Base\Exceptions\InvalidArgumentException();
        }

        $this->oLogger = $oLogger;
        $this->oImapClient->SetLogger($this->oLogger);

        return $this;
    }
}
