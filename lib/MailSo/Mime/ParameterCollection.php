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
class ParameterCollection extends \MailSo\Base\Collection
{
    /**
     * @access protected
     *
     * @param string $sRawParams = ''
     */
    protected function __construct($sRawParams = '')
    {
        parent::__construct();

        if (0 < strlen($sRawParams)) {
            $this->Parse($sRawParams);
        }
    }

    /**
     * @param string $sRawParams = ''
     *
     * @return \MailSo\Mime\ParameterCollection
     */
    public static function NewInstance($sRawParams = '')
    {
        return new self($sRawParams);
    }

    /**
     * @return \MailSo\Mime\Parameter|null
     */
    public function &GetByIndex($iIndex)
    {
        $mResult = null;
        $mResult =& parent::GetByIndex($iIndex);
        return $mResult;
    }

    /**
     * @param array $aList
     *
     * @return \MailSo\Mime\ParameterCollection
     *
     * @throws \MailSo\Base\Exceptions\InvalidArgumentException
     */
    public function SetAsArray($aList)
    {
        parent::SetAsArray($aList);

        return $this;
    }

    /**
     * @param string $sName
     *
     * @return string
     */
    public function ParameterValueByName($sName)
    {
        $sResult = '';
        $sName = trim($sName);

        $aParams =& $this->GetAsArray();
        foreach ($aParams as /* @var $oParam \MailSo\Mime\ParameterCollection */ $oParam) {
            if (strtolower($sName) === strtolower($oParam->Name())) {
                $sResult = $oParam->Value();
                break;
            }
        }

        return $sResult;
    }

    /**
     * @param string $sRawParams
     *
     * @return \MailSo\Mime\ParameterCollection
     */
    public function Parse($sRawParams)
    {
        $this->clear();

        $aDataToParse = preg_split('/\s*;\s*(?=(?:[^"]*"[^"]*")*[^"]*$)/', $sRawParams);

        foreach ($aDataToParse as $sParam) {
            $this->Add(Parameter::CreateFromParameterLine($sParam));
        }

        $this->reParseParameters();

        return $this;
    }

    /**
     * @param bool $bConvertSpecialsName = false
     *
     * @return string
     */
    public function ToString($bConvertSpecialsName = false)
    {
        $aResult = array();
        $aParams =& $this->GetAsArray();
        foreach ($aParams as /* @var $oParam \MailSo\Mime\Parameter */ $oParam) {
            $sLine = $oParam->ToString($bConvertSpecialsName);
            if (0 < strlen($sLine)) {
                $aResult[] = $sLine;
            }
        }

        return 0 < count($aResult) ? implode('; ', $aResult) : '';
    }

    /**
     * @return void
     */
    private function reParseParameters()
    {
        $aDataToReParse = $this->CloneAsArray();
        $sCharset = \MailSo\Base\Enumerations\Charset::UTF_8;

        $this->clear();

        $aPreParams = array();
        foreach ($aDataToReParse as /* @var $oParam \MailSo\Mime\Parameter */ $oParam) {
            $aMatch = array();
            $sParamName = $oParam->Name();

            if (preg_match('/([^\*]+)\*([\d]{1,2})\*/', $sParamName, $aMatch) && isset($aMatch[1], $aMatch[2])
                && 0 < strlen($aMatch[1]) && is_numeric($aMatch[2])) {
                if (!isset($aPreParams[$aMatch[1]])) {
                    $aPreParams[$aMatch[1]] = array();
                }

                $sValue = $oParam->Value();
                $aValueParts = explode('\'\'', $sValue, 2);
                if (is_array($aValueParts) && 2 === count($aValueParts) && 0 < strlen($aValueParts[1])) {
                    $sCharset = $aValueParts[0];
                    $sValue = $aValueParts[1];
                }

                $aPreParams[$aMatch[1]][(int) $aMatch[2]] = $sValue;
            } elseif (preg_match('/([^\*]+)\*/', $sParamName, $aMatch) && isset($aMatch[1])) {
                if (!isset($aPreParams[$aMatch[1]])) {
                    $aPreParams[$aMatch[1]] = array();
                }

                $sValue = $oParam->Value();
                $aValueParts = explode('\'\'', $sValue, 2);
                if (is_array($aValueParts) && 2 === count($aValueParts) && 0 < strlen($aValueParts[1])) {
                    $sCharset = $aValueParts[0];
                    $sValue = $aValueParts[1];
                }

                $aPreParams[$aMatch[1]][0] = $sValue;
            } else {
                $this->Add($oParam);
            }
        }

        foreach ($aPreParams as $sName => $aValues) {
            ksort($aValues);
            $sResult = implode(array_values($aValues));
            $sResult = urldecode($sResult);

            if (0 < strlen($sCharset)) {
                $sResult = \MailSo\Base\Utils::ConvertEncoding(
                    $sResult,
                    $sCharset,
                    \MailSo\Base\Enumerations\Charset::UTF_8
                );
            }

            $this->Add(Parameter::NewInstance($sName, $sResult));
        }
    }

    /**
     * @param mixed $mItem
     * @param bool $bToTop = false
     * @return ParameterCollection
     */
    public function Add($mItem, $bToTop = false)
    {
        return parent::Add($mItem, $bToTop);
    }
}
