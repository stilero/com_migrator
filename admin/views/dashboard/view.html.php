<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-20 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class ShopMigratorViewDashboard extends JView
{
	// Overwriting JView display method
    function display($tpl = null){
        JHTML::stylesheet(JURI::base().'components/com_shopmigrator/assets/css/layout.css');
        JToolBarHelper::title(JText::_('Shop Migrator'), 'shopmigrator48.png');
        JToolBarHelper::preferences('com_shopmigrator');
        parent::display($tpl);
    }
}
