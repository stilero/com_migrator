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

class MigrateDB{
    
    public $driver;
    public $host;
    public $user;
    public $password;
    public $database;
    public $prefix;
    
    public function __construct($host, $user, $password, $database, $driver='mysql', $prefix='') {
        $this->driver = $driver;
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->prefix = $prefix;
    }
    
    function getDB(){
        $option = array();
        $option['driver'] = $this->driver;
        $option['host'] = $this->host;
        $option['user'] = $this->user;
        $option['password'] = $this->password;
        $option['database'] = $this->database;
        $option['prefix'] = $this->prefix;
        $srcDB = & JDatabase::getInstance( $option );
        return $srcDB;
    }
}
