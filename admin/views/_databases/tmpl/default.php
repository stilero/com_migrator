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
?>
<form action="index.php" method="post" name="adminForm">
    <table class="adminlist">
        <thead>
            <tr>
                <th width="10"><?php echo JText::_( 'ID' ); ?></th>
                <th width="10">
                <input type="checkbox"
                    name="toggle"
                    value="" onclick="checkAll(
                    <?php echo count( $this->$items ); ?>);" 
                />
                </th>
                <th><?php echo JText::_('Title'); ?></th>
                <th width="15%"><?php echo JText::_('Shopser'); ?></th>
                <th width="10%"><?php echo JText::_('Date Revued'); ?></th>
                <th width="8%" align="center"><?php echo JText::_('Order'); ?></th>
                <th width="5%" align="center"><?php echo JText::_('Hits'); ?></th>
                <th width="5%" align="center"><?php echo JText::_('Published'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $k = 0;
            $i = 0;
            foreach( $this->items as $row ){
                $checked = JHTML::_('grid.id', $i, $row->id );
                $published = JHTML::_('grid.published', $row, $i );
                $link = JRoute::_('index.php?option='
                    . JRequest::getVar( 'option' )
                    . '&task=edit&cid[]='. $row->id
                    . '&hidemainmenu=1' );
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td><?php echo $row->id; ?></td>
                <td><?php echo $checked; ?></td>
                <td>
                    <a href="<?php echo $link; ?>">
                        <?php echo $row->title; ?>
                    </a>
                </td>
                <td><?php echo $row->revuer; ?></td>
                <td>
                    <?php
                    echo JHTML::_('date', $row->revued, JTEXT::_('%m/%d/%Y'));
                    ?>
                </td>
                <td><input type="text" name="order[] " size="5"
                    value="<?php echo $row->ordering; ?>"
                    class="text_area"
                    style="text-align: center" />
                </td>
                <td align="center"><?php echo $row->hits;?></td>
                <td align="center"><?php echo $published;?></td>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
            } ?>
        </tbody>
    </table>
    <input type="hidden" name="option"
    value="<?php echo JRequest::getVar( 'option' ); ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
</form>