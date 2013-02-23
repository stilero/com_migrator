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
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * HelloWorld Model
 */
class ShopMigratorModelMigration extends JModelItem
{
	protected $_migrateType;
 
	public function getTasks($systemId){
            $db =& JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('t.task');
            $query->from($db->nameQuote('#__shopmigrator_shopsystems').' s');
            $query->innerJoin($db->nameQuote('#__shopmigrator_shopsystems_tasks').' t ON t.shopsystems_id = s.id');
            $query->where('s.id = '.(int)$systemId);
            $query->order('t.ordering');
            $db->setQuery($query);
            $tasks = $db->loadResultArray();
            return $tasks;
	}
        
        public function getClass($systemId){
            $db =& JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('s.*');
            $query->from($db->nameQuote('#__shopmigrator_shopsystems').' s');
            $query->where('s.id = '.(int)$systemId);
            $db->setQuery($query);
            $tasks = $db->loadObject();
            return $tasks;
	}
}