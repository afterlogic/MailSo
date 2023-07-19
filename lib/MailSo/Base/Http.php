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
class Http
{
    /**
     * @var bool
     */
    private $bIsMagicQuotesOn;

    /**
     * @access private
     */
    private function __construct()
    {
        $this->bIsMagicQuotesOn = (bool) @\ini_get('magic_quotes_gpc');
    }

    /**
     * @return \MailSo\Base\Http
     */
    public static function NewInstance()
    {
        return new self();
    }

    /**
     * @staticvar \MailSo\Base\Http $oInstance;
     *
     * @return \MailSo\Base\Http
     */
    public static function SingletonInstance()
    {
        static $oInstance = null;
        if (null === $oInstance) {
            $oInstance = self::NewInstance();
        }

        return $oInstance;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function HasQuery($sKey)
    {
        return isset($_GET[$sKey]);
    }

    /**
     * @param string $sKey
     * @param mixed $mDefault = null
     * @param bool $bClearPercZeroZero = true
     *
     * @return mixed
     */
    public function GetQuery($sKey, $mDefault = null, $bClearPercZeroZero = true)
    {
        return isset($_GET[$sKey]) ? \MailSo\Base\Utils::StripSlashesValue($_GET[$sKey], $bClearPercZeroZero) : $mDefault;
    }

    /**
     * @return array|null
     */
    public function GetQueryAsArray()
    {
        return \is_array($_GET) ? \MailSo\Base\Utils::StripSlashesValue($_GET, true) : null;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function HasPost($sKey)
    {
        return isset($_POST[$sKey]);
    }

    /**
     * @param string $sKey
     * @param mixed $mDefault = null
     * @param bool $bClearPercZeroZero = false
     *
     * @return mixed
     */
    public function GetPost($sKey, $mDefault = null, $bClearPercZeroZero = false)
    {
        return isset($_POST[$sKey]) ? \MailSo\Base\Utils::StripSlashesValue($_POST[$sKey], $bClearPercZeroZero) : $mDefault;
    }

    /**
     * @return array|null
     */
    public function GetPostAsArray()
    {
        return \is_array($_POST) ? \MailSo\Base\Utils::StripSlashesValue($_POST, false) : null;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function HasRequest($sKey)
    {
        return isset($_REQUEST[$sKey]);
    }

    /**
     * @param string $sKey
     * @param mixed $mDefault = null
     *
     * @return mixed
     */
    public function GetRequest($sKey, $mDefault = null)
    {
        return isset($_REQUEST[$sKey]) ? \MailSo\Base\Utils::StripSlashesValue($_REQUEST[$sKey]) : $mDefault;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function HasServer($sKey)
    {
        return isset($_SERVER[$sKey]);
    }

    /**
     * @param string $sKey
     * @param mixed $mDefault = null
     *
     * @return mixed
     */
    public function GetServer($sKey, $mDefault = null)
    {
        return isset($_SERVER[$sKey]) ? $_SERVER[$sKey] : $mDefault;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function HasEnv($sKey)
    {
        return isset($_ENV[$sKey]);
    }

    /**
     * @param string $sKey
     * @param mixed $mDefault = null
     *
     * @return mixed
     */
    public function GetEnv($sKey, $mDefault = null)
    {
        return isset($_ENV[$sKey]) ? $_ENV[$sKey] : $mDefault;
    }

    /**
     * @return string
     */
    public function ServerProtocol()
    {
        return $this->GetServer('SERVER_PROTOCOL', 'HTTP/1.0');
    }

    /**
     * @return string
     */
    public function GetMethod()
    {
        return $this->GetServer('REQUEST_METHOD', '');
    }

    /**
     * @return bool
     */
    public function IsPost()
    {
        return ('POST' === $this->GetMethod());
    }

    /**
     * @return bool
     */
    public function IsGet()
    {
        return ('GET' === $this->GetMethod());
    }

    /**
     * @return bool
     */
    public function IsPut()
    {
        return ('PUT' === $this->GetMethod());
    }

    /**
     * @return string
     */
    public function GetQueryString()
    {
        return $this->GetServer('QUERY_STRING', '');
    }

    /**
     * @return bool
     */
    public function CheckLocalhost($sServer)
    {
        return \in_array(\strtolower(\trim($sServer)), array(
            'localhost', '127.0.0.1', '::1', '::1/128', '0:0:0:0:0:0:0:1'
        ));
    }

    /**
     * @param string $sValueToCheck = ''
     *
     * @return bool
     */
    public function IsLocalhost($sValueToCheck = '')
    {
        if (empty($sValueToCheck)) {
            $sValueToCheck = $this->GetServer('REMOTE_ADDR', '');
        }

        return $this->CheckLocalhost($sValueToCheck);
    }

    /**
     * @return string
     */
    public function GetRawBody()
    {
        static $sRawBody = null;
        if (null === $sRawBody) {
            $sBody = @\file_get_contents('php://input');
            $sRawBody = (false !== $sBody) ? $sBody : '';
        }
        return $sRawBody;
    }

    /**
     * @param string $sHeader
     *
     * @return string
     */
    public function GetHeader($sHeader)
    {
        $sResultHeader = '';
        $sServerKey = 'HTTP_'.\strtoupper(\str_replace('-', '_', $sHeader));
        $sResultHeader = $this->GetServer($sServerKey, '');

        if (0 === \strlen($sResultHeader)) {
            $sServerKey = 'REDIRECT_' . $sServerKey;
            $sResultHeader = $this->GetServer($sServerKey, '');
        }

        if (0 === \strlen($sResultHeader) &&
            \MailSo\Base\Utils::FunctionExistsAndEnabled('apache_request_headers')) {
            $aHeaders = \array_change_key_case(\apache_request_headers());
            $sLowervaseHeader = \strtolower($sHeader);
            if (isset($aHeaders[$sLowervaseHeader])) {
                $sResultHeader = $aHeaders[$sLowervaseHeader];
            }
        }

        return $sResultHeader;
    }

    /**
     * @return array
     */
    public function GetHeaders()
    {
        $aHeaders = array();

        if (\MailSo\Base\Utils::FunctionExistsAndEnabled('apache_request_headers')) {
            $aHeaders = \apache_request_headers();
        }

        return $aHeaders;
    }

    /**
     * @return string
     */
    public function GetScheme()
    {
        $sHttps = \strtolower($this->GetServer('HTTPS', ''));
        return ('on' === $sHttps || ('' === $sHttps && '443' === (string) $this->GetServer('SERVER_PORT', ''))) ? 'https' : 'http';
    }

    /**
     * @return bool
     */
    public function IsSecure()
    {
        return ('https' === $this->GetScheme());
    }

    /**
     * @param bool $bWithRemoteUserData = false
     * @param bool $bWithoutWWW = true
     * @param bool $bWithoutPort = false
     *
     * @return string
     */
    public function GetHost($bWithRemoteUserData = false, $bWithoutWWW = true, $bWithoutPort = false)
    {
        $sHost = $this->GetServer('HTTP_HOST', '');
        if (0 === \strlen($sHost)) {
            $sScheme = $this->GetScheme();
            $sName = $this->GetServer('SERVER_NAME');
            $iPort = (int) $this->GetServer('SERVER_PORT');

            $sHost = (('http' === $sScheme && 80 === $iPort) || ('https' === $sScheme && 443 === $iPort))
                ? $sName : $sName.':'.$iPort;
        }

        if ($bWithoutWWW) {
            $sHost = 'www.' === \substr(\strtolower($sHost), 0, 4) ? \substr($sHost, 4) : $sHost;
        }

        if ($bWithRemoteUserData) {
            $sUser = \trim($this->HasServer('REMOTE_USER') ? $this->GetServer('REMOTE_USER', '') : '');
            $sHost = (0 < \strlen($sUser) ? $sUser.'@' : '').$sHost;
        }

        if ($bWithoutPort) {
            $sHost = \preg_replace('/:[\d]+$/', '', $sHost);
        }

        return $sHost;
    }

    /**
     * @param bool $bCheckProxy = true
     *
     * @return string
     */
    public function GetClientIp($bCheckProxy = true)
    {
        $sIp = '';
        if ($bCheckProxy && null !== $this->GetServer('HTTP_CLIENT_IP', null)) {
            $sIp = $this->GetServer('HTTP_CLIENT_IP', '');
        } elseif ($bCheckProxy && null !== $this->GetServer('HTTP_X_FORWARDED_FOR', null)) {
            $sIp = $this->GetServer('HTTP_X_FORWARDED_FOR', '');
        } else {
            $sIp = $this->GetServer('REMOTE_ADDR', '');
        }

        return $sIp;
    }

    /**
     * @param string $sUrl
     * @param array $aPost = array()
     * @param string $sCustomUserAgent = 'MailSo Http User Agent (v1)'
     * @param int $iCode = 0
     * @param \MailSo\Log\Logger $oLogger = null
     * @param int $iTimeout = 20
     * @param string $sProxy = ''
     * @param string $sProxyAuth = ''
     *
     * @return string|bool
     */
    public function SendPostRequest(
        $sUrl,
        $aPost = array(),
        $sCustomUserAgent = 'MailSo Http User Agent (v1)',
        &$iCode = 0,
        $oLogger = null,
        $iTimeout = 20,
        $sProxy = '',
        $sProxyAuth = ''
    ) {
        $aOptions = array(
            CURLOPT_URL => $sUrl,
            CURLOPT_HEADER => false,
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => \http_build_query($aPost, '', '&'),
            CURLOPT_TIMEOUT => (int) $iTimeout
        );

        if (0 < \strlen($sCustomUserAgent)) {
            $aOptions[CURLOPT_USERAGENT] = $sCustomUserAgent;
        }

        if (0 < \strlen($sProxy)) {
            $aOptions[CURLOPT_PROXY] = $sProxy;
            if (0 < \strlen($sProxyAuth)) {
                $aOptions[CURLOPT_PROXYUSERPWD] = $sProxyAuth;
            }
        }

        $oCurl = \curl_init();
        \curl_setopt_array($oCurl, $aOptions);

        if ($oLogger) {
            $oLogger->Write('cURL: Send post request: '.$sUrl);
        }

        $mResult = \curl_exec($oCurl);

        $iCode = (int) \curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        $sContentType = (string) \curl_getinfo($oCurl, CURLINFO_CONTENT_TYPE);

        if ($oLogger) {
            $oLogger->Write('cURL: Post request result: (Status: '.$iCode.', ContentType: '.$sContentType.')');
            if (false === $mResult || 200 !== $iCode) {
                $oLogger->Write('cURL: Error: '.\curl_error($oCurl), \MailSo\Log\Enumerations\Type::WARNING);
            }
        }

        if (\is_resource($oCurl)) {
            \curl_close($oCurl);
        }

        return $mResult;
    }

    /**
     * @param string $sUrl
     * @param array $aOptions
     * @param \MailSo\Log\Logger $oLogger = null
     *
     * @return string
     */
    public static function DetectAndHackFollowLocationUrl($sUrl, &$aOptions, $oLogger = null)
    {
        $sSafeMode = \strtolower(\trim(@\ini_get('safe_mode')));
        $bSafeMode = 'on' === $sSafeMode || '1' === $sSafeMode;

        $sNewUrl = null;
        $sUrl = isset($aOptions[CURLOPT_URL]) ? $aOptions[CURLOPT_URL] : $sUrl;

        if (isset($aOptions[CURLOPT_FOLLOWLOCATION]) && $aOptions[CURLOPT_FOLLOWLOCATION] && 0 < \strlen($sUrl) &&
            ($bSafeMode || \ini_get('open_basedir') !== '')) {
            $aOptions[CURLOPT_FOLLOWLOCATION] = false;

            $iMaxRedirects = isset($aOptions[CURLOPT_MAXREDIRS]) ? $aOptions[CURLOPT_MAXREDIRS] : 5;
            $iRedirectLimit = $iMaxRedirects;

            if ($iRedirectLimit > 0) {
                $sNewUrl = $sUrl;

                $oCurl = \curl_init($sUrl);

                $aAddOptions = array(
                    CURLOPT_URL => $sUrl,
                    CURLOPT_HEADER => true,
                    CURLOPT_NOBODY => true,
                    CURLOPT_FAILONERROR => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => false,
                    CURLOPT_FORBID_REUSE => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 5
                );

                if (isset($aOptions[CURLOPT_HTTPHEADER]) && \is_array($aOptions[CURLOPT_HTTPHEADER]) && 0 < \count($aOptions[CURLOPT_HTTPHEADER])) {
                    $aAddOptions[CURLOPT_HTTPHEADER] = $aOptions[CURLOPT_HTTPHEADER];
                }

                \curl_setopt_array($oCurl, $aAddOptions);

                do {
                    \curl_setopt($oCurl, CURLOPT_URL, $sNewUrl);

                    $sHeader = \curl_exec($oCurl);
                    if (\curl_errno($oCurl)) {
                        $iCode = 0;
                    } else {
                        $iCode = \curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
                        if ($iCode === 301 || $iCode === 302) {
                            $aMatches = array();
                            \preg_match('/Location:(.*?)\n/', $sHeader, $aMatches);
                            $sNewUrl = \trim(\array_pop($aMatches));

                            if ($oLogger) {
                                $oLogger->Write('cUrl: Location URL: '.$sNewUrl);
                            }
                        } else {
                            $iCode = 0;
                        }
                    }
                } while ($iCode && --$iRedirectLimit);

                \curl_close($oCurl);
                if ($iRedirectLimit > 0 && 0 < \strlen($sNewUrl)) {
                    $aOptions[CURLOPT_URL] = $sNewUrl;
                }
            }
        }

        return null === $sNewUrl ? $sUrl : $sNewUrl;
    }

    /**
     * @param string $sUrl
     * @param resource $rFile
     * @param string $sCustomUserAgent = 'MailSo Http User Agent (v1)'
     * @param string $sContentType = ''
     * @param int $iCode = 0
     * @param \MailSo\Log\Logger $oLogger = null
     * @param int $iTimeout = 10
     * @param string $sProxy = ''
     * @param string $sProxyAuth = ''
     * @param array $aHttpHeaders = array()
     * @param bool $bFollowLocation = true
     *
     * @return bool
     */
    public function SaveUrlToFile(
        $sUrl,
        $rFile,
        $sCustomUserAgent = 'MailSo Http User Agent (v1)',
        &$sContentType = '',
        &$iCode = 0,
        $oLogger = null,
        $iTimeout = 10,
        $sProxy = '',
        $sProxyAuth = '',
        $aHttpHeaders = array(),
        $bFollowLocation = true
    ) {
        if (null === $sCustomUserAgent) {
            $sCustomUserAgent = 'MailSo Http User Agent (v1)';
        }

        if (!is_resource($rFile)) {
            if ($oLogger) {
                $oLogger->Write('cURL: input resource invalid.', \MailSo\Log\Enumerations\Type::WARNING);
            }

            return false;
        }

        $aOptions = array(
            CURLOPT_URL => $sUrl,
            CURLOPT_HEADER => false,
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => !!$bFollowLocation,
            CURLOPT_MAXREDIRS => 7,
            CURLOPT_FILE => $rFile,
            CURLOPT_TIMEOUT => (int) $iTimeout
        );

        if (0 < \strlen($sCustomUserAgent)) {
            $aOptions[CURLOPT_USERAGENT] = $sCustomUserAgent;
        }

        if (0 < \strlen($sProxy)) {
            $aOptions[CURLOPT_PROXY] = $sProxy;
            if (0 < \strlen($sProxyAuth)) {
                $aOptions[CURLOPT_PROXYUSERPWD] = $sProxyAuth;
            }
        }

        if (\is_array($aHttpHeaders) && 0 < \count($aHttpHeaders)) {
            $aOptions[CURLOPT_HTTPHEADER] = $aHttpHeaders;
        }

        if ($oLogger) {
            $oLogger->Write('cUrl: URL: '.$sUrl);
            if (isset($aOptions[CURLOPT_HTTPHEADER]) && \is_array($aOptions[CURLOPT_HTTPHEADER]) && 0 < \count($aOptions[CURLOPT_HTTPHEADER])) {
                $oLogger->Write('cUrl: Headers: '.\print_r($aOptions[CURLOPT_HTTPHEADER], true));
            }
        }

        \MailSo\Base\Http::DetectAndHackFollowLocationUrl($sUrl, $aOptions, $oLogger);

        $oCurl = \curl_init();
        \curl_setopt_array($oCurl, $aOptions);

        $bResult = \curl_exec($oCurl);

        $iCode = (int) \curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        $sContentType = (string) \curl_getinfo($oCurl, CURLINFO_CONTENT_TYPE);

        if ($oLogger) {
            $oLogger->Write('cUrl: Request result: '.($bResult ? 'true' : 'false').' (Status: '.$iCode.', ContentType: '.$sContentType.')');
            if (!$bResult || 200 !== $iCode) {
                $oLogger->Write('cUrl: Error: '.\curl_error($oCurl), \MailSo\Log\Enumerations\Type::WARNING);
            }
        }

        if (\is_resource($oCurl)) {
            \curl_close($oCurl);
        }

        return $bResult;
    }

    /**
     * @param string $sUrl
     * @param string $sCustomUserAgent = 'MailSo Http User Agent (v1)'
     * @param string $sContentType = ''
     * @param int $iCode = 0
     * @param \MailSo\Log\Logger $oLogger = null
     * @param int $iTimeout = 10
     * @param string $sProxy = ''
     * @param string $sProxyAuth = ''
     * @param array $aHttpHeaders = array()
     * @param bool $bFollowLocation = true
     *
     * @return string|bool
     */
    public function GetUrlAsString(
        $sUrl,
        $sCustomUserAgent = 'MailSo Http User Agent (v1)',
        &$sContentType = '',
        &$iCode = 0,
        $oLogger = null,
        $iTimeout = 10,
        $sProxy = '',
        $sProxyAuth = '',
        $aHttpHeaders = array(),
        $bFollowLocation = true
    ) {
        $rMemFile = \MailSo\Base\ResourceRegistry::CreateMemoryResource();
        if ($this->SaveUrlToFile($sUrl, $rMemFile, $sCustomUserAgent, $sContentType, $iCode, $oLogger, $iTimeout, $sProxy, $sProxyAuth, $aHttpHeaders, $bFollowLocation) && \is_resource($rMemFile)) {
            \rewind($rMemFile);
            return \stream_get_contents($rMemFile);
        }

        return false;
    }

    /**
     * @param int $iExpireTime
     * @param bool $bSetCacheHeader = true
     * @param string $sEtag = ''
     *
     * @return bool
     */
    public function ServerNotModifiedCache($iExpireTime, $bSetCacheHeader = true, $sEtag = '')
    {
        $bResult = false;
        if (0 < $iExpireTime) {
            $iUtcTimeStamp = \time();
            $sIfModifiedSince = $this->GetHeader('If-Modified-Since');
            if (0 === \strlen($sIfModifiedSince)) {
                if ($bSetCacheHeader) {
                    \header('Cache-Control: public', true);
                    \header('Pragma: public', true);
                    \header('Last-Modified: '.\gmdate('D, d M Y H:i:s', $iUtcTimeStamp - $iExpireTime).' UTC', true);
                    \header('Expires: '.\gmdate('D, j M Y H:i:s', $iUtcTimeStamp + $iExpireTime).' UTC', true);

                    if (0 < strlen($sEtag)) {
                        \header('Etag: '.$sEtag, true);
                    }
                }
            } else {
                $this->StatusHeader(304);
                $bResult = true;
            }
        }

        return $bResult;
    }

    /**
     * @staticvar bool $bCache
     */
    public function ServerNoCache()
    {
        static $bCache = false;
        if (false === $bCache) {
            $bCache = true;
            @\header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            @\header('Last-Modified: '.\gmdate('D, d M Y H:i:s').' GMT');
            @\header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            @\header('Cache-Control: post-check=0, pre-check=0', false);
            @\header('Pragma: no-cache');
            @\header('X-AfterLogic-Cache: no');
        }
    }

    /**
     * @staticvar bool $bCache
     * @param string $sEtag
     * @param int $iLastModified
     * @param int $iExpires
     */
    public function ServerUseCache($sEtag, $iLastModified, $iExpires)
    {
        static $bCache = false;
        if (false === $bCache) {
            $bCache = true;
            @\header('Cache-Control: private', true);
            @\header('ETag: '.$sEtag, true);
            @\header('Last-Modified: '.\gmdate('D, d M Y H:i:s', $iLastModified).' UTC', true);
            @\header('Expires: '.\gmdate('D, j M Y H:i:s', $iExpires).' UTC', true);
            @\header('X-AfterLogic-Cache: yes');
        }
    }

    /**
     * @param int $iStatus
     *
     * @return void
     */
    public function StatusHeader($iStatus, $sCustomStatusText = '')
    {
        $iStatus = (int) $iStatus;
        if (99 < $iStatus) {
            $aStatus = array(
                301 => 'Moved Permanently',
                304 => 'Not Modified',
                200 => 'OK',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed'
            );

            $sCustomStatusText = \trim($sCustomStatusText);
            $sHeaderHead = \ini_get('cgi.rfc2616_headers') && false !== \strpos(\strtolower(\php_sapi_name()), 'cgi') ? 'Status:' : $this->ServerProtocol();
            $sHeaderText = (0 === \strlen($sCustomStatusText) && isset($aStatus[$iStatus]) ? $aStatus[$iStatus] : $sCustomStatusText);

            \header(\trim($sHeaderHead.' '.$iStatus.' '.$sHeaderText), true, $iStatus);
        }
    }

    /**
     * @return string
     */
    public function GetPath()
    {
        $sUrl = \ltrim(\substr($this->GetServer('SCRIPT_NAME', ''), 0, \strrpos($this->GetServer('SCRIPT_NAME', ''), '/')), '/');
        return '' === $sUrl ? '/' : '/'.$sUrl.'/';
    }

    /**
     * @return string
     */
    public function GetUrl()
    {
        return $this->GetServer('REQUEST_URI', '');
    }

    /**
     * @return string
     */
    public function GetFullUrl()
    {
        return $this->GetScheme().'://'.$this->GetHost(true, false).$this->GetPath();
    }
}
