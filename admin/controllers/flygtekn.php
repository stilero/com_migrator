<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-17 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of opencart.
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

// no direct access
defined('_JEXEC') or die('Restricted access'); 

// import joomla controller library
jimport('joomla.application.component.controller');

//Generic Classes
JLoader::register('Migrate', SHOPMIGRATOR_CLASSES.DS.'migrate.php');
JLoader::register('MigrateDB', SHOPMIGRATOR_CLASSES.DS.'MigrateDB.php');
JLoader::register('MigrateError', SHOPMIGRATOR_CLASSES.DS.'MigrateError.php');
JLoader::register('MigrateTable', SHOPMIGRATOR_CLASSES.DS.'MigrateTable.php');
JLoader::register('MigrateURL', SHOPMIGRATOR_CLASSES.DS.'MigrateURL.php');
JLoader::register('MigrateDB', SHOPMIGRATOR_CLASSES.DS.'MigrateDB.php');
JLoader::register('MigrateFiles', SHOPMIGRATOR_CLASSES.DS.'MigrateFiles.php');

//JLoader::register('VmConfig', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

class ShopMigratorControllerFlygtekn extends JController{
    
    public static $viewName = 'flygtekn';
    public static $className = 'flygtekn';
    private $_migrateType;
    private $_migrateTask;
    private $_mainView;
    private $mainClasses = array(
            'members'
        );
    
    public function __construct($config = array()) {
        parent::__construct($config);
        //HelperClasses
        JLoader::register('MigrateCountries', SHOPMIGRATOR_CLASSES.DS.self::$className.DS.'migrate.php');
        JLoader::register('MigrateCurrencies', SHOPMIGRATOR_CLASSES.DS.self::$className.DS.'migrate.php');
        JLoader::register('MigrateStatus', SHOPMIGRATOR_CLASSES.DS.self::$className.DS.'migrate.php');
        $this->_mainView =& $this->getView( self::$viewName, 'raw' );
        $migrateCmd = JRequest::getVar('migrateCmd');
        $migrateCmds = explode('.', $migrateCmd);
        $this->_migrateType = $migrateCmds[0];
        $this->_migrateTask = $migrateCmds[1];
        $foundRequestedClass = JFile::exists(SHOPMIGRATOR_CLASSES.DS.self::$className.DS.'migrate.'.$this->_migrateType.'.php');
        $foundRequestedView = JFile::exists(JPATH_COMPONENT.DS.'views'.DS.self::$className.DS.'tmpl'.DS.$this->_migrateType.'.php');
        if($foundRequestedClass && $foundRequestedView){
            $this->_mainView->setLayout($this->_migrateType);
        }
        $this->registerClasses();
        $this->configure();
    }
    
    protected function registerClasses(){
        foreach ($this->mainClasses as $mainClass) {
            JLoader::register('Migrate'.JString::ucfirst($mainClass), SHOPMIGRATOR_CLASSES.DS.self::$className.DS.'migrate.'.$mainClass.'.php');
        }
    }
    
    private function configure(){
        $params = & JComponentHelper::getParams('com_shopmigrator');
        $src = array(
            'db_host' => $params->get('db_host'),
            'db_name' => $params->get('db_name'),
            'db_prefix' => $params->get('db_prefix', ''),
            'db_user' => $params->get('db_user'),
            'db_pass' => $params->get('db_pass'),
            'db_type' => 'mysql',
        );
        $app =& JFactory::getApplication();
        $dest = array(
            'db_host' => $app->getCfg('host'),
            'db_name' => $app->getCfg('db'),
            'db_prefix' => $app->getCfg('dbprefix', ''),
            'db_user' => $app->getCfg('user'),
            'db_pass' => $app->getCfg('password'),
            'db_type' => $app->getCfg('dbtype'),
        );
        $srcMigrateDB = new MigrateDB($src['db_host'], $src['db_user'], $src['db_pass'], $src['db_name'], $src['db_type'], $src['db_prefix']);
        $srcDB = & $srcMigrateDB->getDB();
        $MigrateDestDB = new MigrateDB($dest['db_host'], $dest['db_user'], $dest['db_pass'], $dest['db_name'], $dest['db_type'], $dest['db_prefix']);
        $destDB = & $MigrateDestDB->getDB();
        $storeUrl = $params->get('shopurl');
        $this->_mainView->assignRef('srcDB', $srcDB);
        $this->_mainView->assignRef('destDB', $destDB);
        $this->_mainView->assignRef('storeUrl', $storeUrl);
        $this->_mainView->assignRef('migrateTask', $this->_migrateTask);
    }


    public function display(){
        $this->_mainView->display();
    }
}
