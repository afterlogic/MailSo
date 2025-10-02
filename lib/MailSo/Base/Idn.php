<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base;

class Idn
{
    public function encode($url)
    {
        return idn_to_ascii($url);
    }

    public function decode($url)
    {
        return idn_to_utf8($url);
    }

    public function encodeEmail($email)
    {
        $sUser = Utils::GetAccountNameFromEmail($email);
        $sDomain = Utils::GetDomainFromEmail($email);

        return $sUser . '@' . $this->encode($sDomain);
    }

    public function decodeEmail($email)
    {
        $sUser = Utils::GetAccountNameFromEmail($email);
        $sDomain = Utils::GetDomainFromEmail($email);

        return $sUser . '@' . $this->decode($sDomain);
    }
}
