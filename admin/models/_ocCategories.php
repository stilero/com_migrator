<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-08 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of ocCategories.
 * 
 * ShopMigrator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ShopMigrator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with ShopMigrator.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.model');
 
class ShopMigratorModelOcCategories extends JModel{
    protected $_items;
    private $_table;
    private $_cat_to_store_table;
    static private $_tableName = '#__shopmigrator_ocCategories';
    static private $_tableName_cat_to_store = '#__shopmigrator_ocCategories';

    public function __construct() {
        parent::__construct();
        $db =& JFactory::getDbo();
        $this->_table = $db->nameQuote(self::$_tableName);
        $this->_cat_to_store_table = $db->nameQuote(self::$_tableName_cat_to_store);
    }
    public function getItemsForStore($storeid=0){
        $db =& JFactory::getDbo();
        $query = "SELECT b.* FROM ".$this->_cat_to_store_table." a"
                ." INNER JOIN ".$this->_table." b"
                ." ON a.category_id = b.category_id"
                ." WHERE a.store = ".(int)$storeid;
        $db->setQuery($query);
        $this->_items = $db->loadObjectList();
        return $this->_items;
    }
    
    public function getDescriptionForLang($langcode){
        $db =& JFactory::getDbo();
        $langKey = $db->nameQuote('language_id');
        $langVal = (int)$langcode;
        $query = "SELECT * FROM ".$this->_table
                ." WHERE ".$langKey." = ".$langVal;
        $db->setQuery($query);
        $this->_items = $db->loadObjectList();
        return $this->_items;
    }
    
}