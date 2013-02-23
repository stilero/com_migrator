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
 
// import Joomla view library
jimport('joomla.application.component.view');
JLoader::register('MigrateEntity', dirname(__FILE__).DS.'tmpl'.DS.'entity.php');
 
/**
 * HTML View class for the HelloWorld Component
 */
class ShopMigratorViewModx extends JView{
    
    function display($tpl = null) {
        parent::display($tpl);
    }
}
