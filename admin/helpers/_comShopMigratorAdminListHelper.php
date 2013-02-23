<?php
/**
 * AdminListHelper - For fast creation of Admin Lists
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-08 Stilero Webdesign, Stilero AB
 * @category Plugins
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of comShopMigratorAdminListHelper.
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

class comShopMigratorAdminListHelper{
    /**
     * Returns a header for the Admin List
     * @param Array $headings - Headings to display
     *                              $headings = array(
                                        array(
     *                                      'width' => '8%', 
     *                                      'text' => 'Heading Text', 
     *                                      'align' =>'center')
     *                              );
     * @param int $numOfItems
     * @return string
     */
    public static function getFormListHeader($headings, $numOfItems){
        $html = '<form action="index.php" method="post" name="adminForm">'.
                '<table class="adminlist">'.
                '<thead>'.
                    '<tr>'.
                '<th width="10">'.JText::_( 'ID' ).'</th>'.
                '<th width="10">'.
                '<input type="checkbox" name="toggle" value="" onclick="checkAll('.
                    $numOfItems.');" '.
                '/>'.
                '</th>';
        foreach ($headings as $heading) {
            $width = isset($heading['width']) ? ' width="'.$heading['width'].'"' : '';
            $align = isset($heading['align']) ? ' align="'.$heading['align'].'"' : '';
            $text = isset($heading['text']) ? JText::_($heading['text']) : '';
            $html .= '<th'.$width.$align.'>'.$text.'</th>';
        }
        $html .= '</tr>'.
        '</thead>';
        return $html;
    }
    
    /**
     * 
     * @param Array $items - All items to use directly from the DB
     * @return string
     */
    public static function getFormListBody($items){
        $html = '<tbody>';
        if($items === null){
            $html .= '<tr>No items found</tr>';
            return $html;
        }
        $k = 0;
        $i = 0;
        foreach( $items as $row ){
            $checked = JHTML::_('grid.id', $i, $row->id );
            $link = JRoute::_('index.php?option='.JRequest::getVar( 'option' ).'&task=edit&cid[]='. $row->id. '&hidemainmenu=1' );
            $html = '<tr class="row'.$k.'">';
            $z = 0;
            foreach ($row as $val){
                if($z == 1){
                    $html .= '<td>'.$checked.'</td>';
                }
                $html .= '<td><a href="'.$link.'">'.$val.'</a></td>';
                $z++;
            }    
            $html .= '</tr>';
            $k = 1 - $k;
            $i++;
        }
        $html .= '</tbody>';
        return $html;
    }
    
    /**
     * A Footer that ends the list
     * @return string
     */
    public static function getForListFooter(){
        $html = 
            '</table>'.
            '<input type="hidden" name="option" value="'.JRequest::getVar( 'option' ).'" />'.
            '<input type="hidden" name="view" value="'.JRequest::getCmd( 'view' ).'" />'.
            '<input type="hidden" name="task" value="" />'.
            '<input type="hidden" name="boxchecked" value="0" />'.
            '<input type="hidden" name="hidemainmenu" value="0" />'.
        '</form>';
        return $html;
    }
}