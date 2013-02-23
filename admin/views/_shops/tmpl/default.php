<?php
/**
 * Description of ShopMigrator
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
 * This file is part of shops.
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
defined('_JEXEC') or die('Restricted access');?>
<?php 
$headings = array(
    array('text' => 'Name'),
    array('width' => '15%', 'text' => 'Url'),
    array('width' => '15%', 'text' => 'DB'),
    array('width' => '15%', 'text' => 'System'),
    array('width' => '10%', 'text' => 'Status'),
    array('width' => '10%', 'text' => 'Created'),
); 
print comShopMigratorAdminListHelper::getFormListHeader($headings, count($this->items));
print comShopMigratorAdminListHelper::getFormListBody($this->items);
print comShopMigratorAdminListHelper::getForListFooter();
?>