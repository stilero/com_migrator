<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-09 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class MigrateTable{
    
    public static function getQuotedTable($table, $prefix=''){
        $db =& JFactory::getDbo();
        $quotedTable = $db->nameQuote(str_replace('#__', $prefix, $table));
    }
    
    public static function getQuotedJTable($table){
        $app =& JFactory::getApplication();
        $dbPrefix = $app->getCfg('dbprefix');
        $db =& JFactory::getDbo();
        $quotedTable = $db->nameQuote(str_replace('#__', $dbPrefix, $table));
        return $quotedTable;
    }
}
