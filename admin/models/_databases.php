<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-16 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.model');
 
class ShopMigratorModelDatabases extends MigratorModel{
    
    static private $_table = '#__shopmigrator_databases';
    static private $_tableClass = 'databases';

    public function __construct() {
        parent::__construct(self::$_table, self::$_tableClass);
    }
}