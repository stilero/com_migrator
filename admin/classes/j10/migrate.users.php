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

class MigrateUsers extends Migrate{
    
    //OC tables
    protected static $_customerTable = '#__customer';
    protected static $_AddressTable = '#__address';
    protected static $_countryTable = '#__country';
    //VM Tables
    protected static $_vmUsersTable = '#__users';
    protected static $_vmCountryTable = '#__virtuemart_countries';
    protected static $_vmUserInfosTable = '#__virtuemart_userinfos';


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0, $vmCurrencyCode=144) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
    }
       
    public function getData(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('u.*, a.*, c.iso_code_2');
        $query->from(self::$_customerTable.' u');
        $query->innerJoin(self::$_AddressTable.' a ON a.customer_id = u.customer_id');
        $query->innerJoin(self::$_countryTable.' c ON c.country_id = a.country_id');
        $query->group('a.customer_id');
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function clearData(){
        $isSuccessful = true;
        $tables = array(
            self::$_vmUsersTable
        );
        $db =& $this->_destDB;
        foreach ($tables as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $query->where('id NOT IN(837,838,839,840)');
            $db->setQuery($query);
            $result = $db->query();
            if(!$result){
               $isSuccessful *= false;
            }
        }
        $tables2 = array(
            self::$_vmUserInfosTable
        );
        foreach ($tables2 as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $query->where('virtuemart_user_id NOT IN(837,838,839,840)');
            $db->setQuery($query);
            $result = $db->query();
            if(!$result){
               $isSuccessful *= false;
            }
        }
        return (bool)$isSuccessful;
    }
    
    public function hasNoConflict(){
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('customer_id');
        $query->from($db->nameQuote(self::$_customerTable));
        $db->setQuery($query);
        $srcIds = $db->loadResultArray();
        $db2 =& $this->_destDB;
        $query2 = $db2->getQuery(true);
        $query2->select('id');
        $query2->from($db2->nameQuote(self::$_vmUsersTable));
        $query2->where('id IN ('.implode(',', $srcIds).')');
        $db2->setQuery($query2);
        $result = $db2->loadResultArray();
        if($result){
            $this->setError(MigrateError::DB_ERROR, 'Conflict detected. Users already exists with ID: '.implode(', ', $result));
            return false;
        }
        return true;
    }
    
    protected function setUser($user){
        $db =& $this->_destDB;
        $tempPass = substr(base64_encode(uniqid()), 0, 8);
        $blocked = $user->status == 1 ? 0 : 1;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmUsersTable);
        $query->set('id = '.(int)$user->customer_id);
        $query->set('name = '.$db->quote($user->firstname.' '.$user->lastname));
        $query->set('username = '.$db->quote($user->email));
        $query->set('email = '.$db->quote($user->email));
        $query->set('password = '.$db->quote($tempPass));
        $query->set('usertype = 2');
        $query->set('block = '.(int)$blocked);
        $query->set('sendEmail = 0');
        $query->set('registerDate = '.$db->quote($user->date_added));
        $query->set('params = '.$db->quote('{}'));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed saving user to DB with id:'.$user->customer_id);
            return false;
        }
        return true;
    }
    
    protected function getVMCountryCodeForISO($twoLetterIsoCode){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from(self::$_vmCountryTable);
        $query->where('country_2_code = '.$db->quote($twoLetterIsoCode));
        $db->setQuery($query);
        $item = $db->loadObject();
        return $item;
    }
    
    protected function setUserInfo($user){
        $isSuccessful = true;
        $vmCountryCode = $this->getVMCountryCodeForISO($user->iso_code_2);
        $db =& $this->_destDB;
        $tempPass = substr(base64_encode(uniqid()), 0, 8);
        $query = $db->getQuery(true);
        $query->insert(self::$_vmUserInfosTable);
        $query->set('virtuemart_user_id = '.(int)$user->customer_id);
        $query->set('address_type = '.$db->quote('BT'));
        $query->set('name = '.$db->quote($user->firstname.' '.$user->lastname));
        $query->set('company = '.$db->quote($user->company));
        $query->set('title = '.$db->quote('Mr'));
        $query->set('last_name = '.$db->quote($user->lastname));
        $query->set('first_name = '.$db->quote($user->firstname));
        $query->set('phone_1 = '.$db->quote($user->telephone));
        $query->set('fax = '.$db->quote($user->fax));
        $query->set('address_1 = '.$db->quote($user->address_1));
        $query->set('address_2 = '.$db->quote($user->address_2));
        $query->set('city = '.$db->quote($user->city));
        $query->set('virtuemart_country_id = '.(int)$vmCountryCode->virtuemart_country_id);
        $query->set('zip = '.$db->quote($user->postcode));
        $query->set('agreed = '.$db->quote($user->approved));
        $query->set('created_on = '.$db->quote($user->date_added));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting user info for user with id id:'.$user->customer_id);
            return false;
        }
        return true;
    }
    
    public function migrateUsers(){
        $isSuccessful = true;
        $users = $this->getData();
        foreach ($users as $user) {
            $isSuccessful *= $this->setUser($user);
            $isSuccessful *= $this->setUserInfo($user);
        }
        return (bool)$isSuccessful;
    }
}
