<?php
/**
 * This file is part of the "FS19 Web Stats" package.
 * Copyright (C) 2018 John Hawk <john.hawk@gmx.net>
 *
 * "FS19 Web Stats" is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "FS19 Web Stats" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
ini_set ( 'error_reporting', E_ALL );
ini_set ( 'display_errors', 1 );
ini_set ( 'log_errors', 1 );
ini_set ( 'error_log', 'error.log' );

session_start ();
define ( 'IN_FS19WS', true );

// Change next lines if you are not german ;-)
if (function_exists ( 'date_default_timezone_set' )) {
	date_default_timezone_set ( 'Europe/Berlin' );
}

$defaultStyle = 'fs19webstats';

require ('./include/smarty/Smarty.class.php');
require ('./include/language.php');
require ('./include/functions.php');

// Load cookie with user settings
include ('./include/coockie.php');
$style = $options ['general'] ['style'];

$smarty = new Smarty ();
$smarty->debugging = false;
$smarty->caching = false;
$smarty->assign ( 'webStatsVersion', '2.0.0-0001 (29.11.2018)' );

include ('./include/loadConfig.php');
$smarty->assign ( 'onlineUser', sizeof ( $onlineUser ) );
$smarty->assign ('hideFooter',$options ['general'] ['hideFooter']);

//require ('./include/savegame.php');
$serverOnline = true;

// Allowed/existing pages
$pages = array (
		'overview',
		'husbandry',
		'storage',
		'production',
		'commodity',
		'options',
		'lizenz',
		'finances',
		'prices',
		'factories'
);
$page = GetParam ( 'page', 'G' );
if (! in_array ( $page, $pages )) {
	$page = 'prices';
}
$smarty->assign ( 'page', $page );
if ($serverOnline) {
	include ("./include/$page.php");
}
$smarty->assign ( 'reloadPage', $options ['general'] ['reload'] );
$smarty->assign ( 'serverOnline', $serverOnline );
$smarty->setTemplateDir ( "./styles/$style/templates" );
$smarty->assign ( 'style', $style );
$tpl_source = $smarty->fetch ( 'index.tpl', $style, $style );

echo preg_replace_callback ( '/##(.+?)##/', 'prefilter_i18n', $tpl_source );