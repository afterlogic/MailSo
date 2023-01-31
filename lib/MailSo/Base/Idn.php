<?php
/**
 * This code is licensed under AGPLv3 license or Afterlogic Software License
 * if commercial version of the product was purchased.
 * For full statements of the licenses see LICENSE-AFTERLOGIC and LICENSE-AGPL3 files.
 */

namespace MailSo\Base;

use Symfony\Polyfill\Intl\Idn as p;

class Idn
{
    public function encode($url)
    {
        if (function_exists('idn_to_ascii')) {
            return idn_to_ascii($url);
        } else {
            return p\Idn::idn_to_ascii($url);
        }
    }

    public function decode($url)
    {
        if (function_exists('idn_to_utf8')) {
            return idn_to_utf8($url);
        } else {
            return p\Idn::idn_to_utf8($url);
        }
    }
}
