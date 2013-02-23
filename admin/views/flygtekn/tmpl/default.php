<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-17 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$class = 'O:8:"stdClass":3:{s:4:"name";s:15:"Daniel Eliasson";s:8:"username";s:0:"";s:6:"fields";a:11:{s:13:"foedelsedatum";s:8:"12345678";s:14:"telefon-arbete";s:11:"031-3609040";s:13:"adress-arbete";s:13:"Adress arbete";s:17:"postadress-arbete";s:11:"12345 tewet";s:10:"adress-hem";s:18:"Tranbärsvägen 52";s:14:"postadress-hem";s:11:"44837 Floda";s:11:"telefon-hem";s:10:"0302-35299";s:5:"titel";s:11:"Webdesigner";s:12:"arbetsgivare";s:13:"APM Terminals";s:9:"avdelning";a:1:{i:0;s:9:"Göteborg";}s:6:"reklam";s:2:"Ja";}}';
var_dump(unserialize($class));exit;
?>
<!DOCTYPE html>
<html>
    <head></head>
    <title>Testing</title>
    <body><p>Works</p></body>
</html>