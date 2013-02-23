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
 * This file is part of edit.
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
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col100">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'Details' ); ?></legend>
            <table class="admintable">
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="name">
                        <?php echo JText::_( 'Name' ); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text"
                        name="name" id="name" size="25"
                        value="<?php echo $this->item->name;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="url">
                            <?php echo JText::_( 'URL' ); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text"
                        name="url" id="url" size="10"
                        value="<?php echo $this->item->url;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="db_id">
                            <?php echo JText::_( 'Database' ); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="text_area" type="text"
                        name="db_id" id="db_id"
                        size="32" maxlength="250"
                        value="<?php echo $this->item->db_id;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="shop_system_id">
                            <?php echo JText::_( 'Shop system' ); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text"
                        name="shop_system_id" id="shop_system_id" size="50"
                        value="<?php echo $this->item->shop_system_id;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="status_id">
                            <?php echo JText::_( 'Status' ); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text" 
                               name="status_id" id="status_id" size="10" maxlength="5" 
                               value="<?php echo $this->item->status_id;?>" />
                    </td>
                </tr>
                <tr>
                    <td width="100" align="right" class="key">
                        <label for="created">
                            <?php echo JText::_( 'Created' ); ?>:
                        </label>
                    </td>
                    <td>
                        <?php echo JHTML::_( 'calendar',
                        JHTML::_('date',
                        $this->item->created,
                        JTEXT::_('m/d/Y')),
                        'created',
                        'created',
                        'm/d/Y',
                        array( 'class'=>'inputbox',
                        'size'=>'25',
                        'maxlength'=>'19' ) ); ?>
                    </td>
                </tr>
               
            </table>
        </fieldset>
    </div>
    <div class="clr"></div>
    <input type="hidden" name="option"
    value="<?php echo JRequest::getVar( 'option' ); ?>" />
    <input type="hidden" name="view"
           value="<?php echo JRequest::getCmd( 'view' ); ?>" />
    <input type="hidden" name="id"
    value="<?php echo $this->item->id; ?>" />
    <input type="hidden" name="task" value="" />
</form>