<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-15 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class MigrateCountries{
    
    protected $srcDB;
    protected $srcTable;
    protected $srcIdColumn;
    protected $srcIsoCodeColumn;
    
    public function __construct($srcDB, $srcTable, $srcColumn) {
        $this->srcDB = $srcDB;
        $this->srcTable = $srcTable;
        $this->srcIsoCodeColumn = $srcColumn;
    }
    
    public function getCountryIdForCode($isoCode){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($this->srcIdColumn);
        $query->from($this->srcTable);
        $query->where($this->srcIsoCodeColumn.' = '.$db->quote($isoCode));
        $db->setQuery($query);
        $item = $db->loadColumn();
        if(isset($item[0])){
            return $item[0];
        }
        return false;
    }
}
