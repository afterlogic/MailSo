<?php

/*
 * Copyright 2004-2015, AfterLogic Corp.
 * Licensed under AGPLv3 license or AfterLogic license
 * if commercial version of the product was purchased.
 * See the LICENSE file for a full license statement.
 */

namespace MailSo;

if (!\defined('MAILSO_LIBRARY_ROOT_PATH'))
{
	\define('MAILSO_LIBRARY_ROOT_PATH', \defined('MAILSO_LIBRARY_USE_PHAR')
		? 'phar://mailso.phar/' : \rtrim(\realpath(__DIR__), '\\/').'/');
}

}