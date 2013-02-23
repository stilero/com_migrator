<?php
/**
 * Description of Shop_Migrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-07 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of admin.shopmigrator.
 * 
 * Shop_Migrator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Shop_Migrator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Shop_Migrator.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

JHTML::addIncludePath(JPATH_COMPONENT.DS.'helpers');
require_once JPATH_COMPONENT.DS.'controller.php';
//require_once JPATH_COMPONENT.DS.'helpers'.DS.'comShopMigratorAdminListHelper.php';
define('SHOPMIGRATOR_CLASSES', JPATH_COMPONENT.DS.'classes');
JLoader::register('MigratorModel', JPATH_COMPONENT.DS.'models'.DS.'model.php');
$controller = JRequest::getWord('view');

if ( $controller) { 
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if ( file_exists($path)) {
        require_once $path;
    } else {       
        $controller = '';	   
    }
}
$classname    = 'ShopmigratorController'.$controller;
$controller   = new $classname();

// Perform the Request task
$controller->execute(JRequest::getCmd('task', 'display'));
 
// Redirect if set by the controller
$controller->redirect();