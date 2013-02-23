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

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class MigrateOrders extends Migrate{
    
    protected static $_orderTable = '#__order';
    protected static $_orderTotalTable = '#__order_total';
    protected static $_vmOrdersTable = '#__virtuemart_orders';
    
    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
    }
    
    public function getData($orderTotalCode = 'total'){
//        if(isset($this->_sourceData)){
//            return $this->_sourceData;
//        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('o.*, ot.value AS order_total');
        $query->from($db->nameQuote(self::$_orderTotalTable).' ot');
        $query->innerJoin($db->nameQuote(self::$_orderTable).' o ON o.order_id = ot.order_id');
        $query->where('ot.code = '.$db->quote($orderTotalCode));
        print $query->dump();
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function clearData(){
        $isSuccessful = true;
        $tables = array(
            self::$_vmOrdersTable
        );
        $db =& $this->_destDB;
        foreach ($tables as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $query->where('virtuemart_order_id > 2');
            $db->setQuery($query);
            $result = $db->query();
            if(!$result){
               $isSuccessful *= false;
            }
        }
        return (bool)$isSuccessful;
    }
    
    protected function setOrder($order){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmOrdersTable);
        $query->set('virtuemart_order_id = '.(int)$order->order_id);
        $query->set('virtuemart_user_id = '.(int)$order->customer_id);
        $query->set('virtuemart_vendor_id = 1');
        $query->set('order_number = '.(int)$order->invoice_no);
        $query->set('order_total = '.(int)$order->order_total);
        $query->set('order_salesPrice = '.(int)$order->order_total);
        $query->set('order_status = '.(int)  MigrateStatus::convertToVmOrderStatus($order->order_status_id));
        $query->set('created_on = '.$db->quote($order->date_added));
        $query->set('modified_on = '.$db->quote($order->date_modified));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            return false;
        }
        return true;
    }
    
    protected function setOrderSubTotal($order){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->update(self::$_vmOrdersTable);
        $query->set('order_subtotal = '.(int)$order->order_total);
        $query->where('virtuemart_order_id = '.(int)$order->order_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            return false;
        }
        return true;
    }
    
    public function migrateOrders(){
        $isSuccessful = true;
        $items = $this->getData();
        foreach ($items as $item) {
            $isSuccessful *= $this->setOrder($item);
        }
        return (bool)$isSuccessful;
    }
}
