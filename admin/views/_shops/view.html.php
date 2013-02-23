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
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 */
class ShopMigratorViewShops extends JView{
    
    function display($tpl = null) {
        JToolBarHelper::title(JText::_('ShopMigrator Shops', 'generic.png'));
        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();
        JToolBarHelper::preferences('com_shopmigrator');
        $model =& $this->getModel('shops');
        $items =& $model->getItems();
        
        $this->assignRef('items', $items);
        parent::display($tpl);
    }
    
    function edit($id) {
        JToolBarHelper::title(JText::_('ShopMigrator Shops: [<small>Edit</small>]', 'generic.png'));
        JToolBarHelper::save();
        JToolBarHelper::cancel('cancel', 'Close');
        $model =& $this->getModel();
        $this->form = $this->get('Form');
        $item = $model->getItem( $id );
        $this->assignRef('item', $item);
        parent::display();
    }
    
    function add(){
        JToolBarHelper::title( JText::_('ShopMigrator Shops')
                . ': [<small>Add</small>]' );
        JToolBarHelper::save();
        JToolBarHelper::cancel();
        $model =& $this->getModel();
        $this->form = $model->getForm('com_shopmigrator', 'shops');
        $item = $model->getNewItem();
        $this->assignRef('item', $item);
        parent::display();
    }
}
