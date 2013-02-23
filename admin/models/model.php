<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-16 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 
// import Joomla modelitem library
//jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');


class MigratorModel extends JModelAdmin{
    
    protected $_tableName;
    protected $_tableClassName;
    
    public function __construct($tableName, $tableClassName) {
        parent::__construct();
        $this->_tableName = $tableName;
        $this->_tableClassName = $tableClassName;
    }
    
    public function getForm($name='com_weblinks.weblink', $source='weblink', $data = array(), $loadData = true){
        // Initialise variables.
        $app	= JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm($name, $source, array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

//        // Determine correct permissions to check.
//        if ($this->getState($source.'.id')) {
//                // Existing record. Can only edit in selected categories.
//                $form->setFieldAttribute('catid', 'action', 'core.edit');
//        } else {
//                // New record. Can only create in selected categories.
//                $form->setFieldAttribute('catid', 'action', 'core.create');
//        }
//
//        // Modify the form based on access controls.
//        if (!$this->canEditState((object) $data)) {
//                // Disable fields for display.
//                $form->setFieldAttribute('ordering', 'disabled', 'true');
//                $form->setFieldAttribute('state', 'disabled', 'true');
//                $form->setFieldAttribute('publish_up', 'disabled', 'true');
//                $form->setFieldAttribute('publish_down', 'disabled', 'true');
//
//                // Disable fields while saving.
//                // The controller has already verified this is a record you can edit.
//                $form->setFieldAttribute('ordering', 'filter', 'unset');
//                $form->setFieldAttribute('state', 'filter', 'unset');
//                $form->setFieldAttribute('publish_up', 'filter', 'unset');
//                $form->setFieldAttribute('publish_down', 'filter', 'unset');
//        }

        return $form;
    }
    
    protected function loadFormData($name='com_weblinks.weblink', $source='weblink'){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($name.'.edit.'.$source.'.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState($source.'.id') == 0) {
                $app = JFactory::getApplication();
                $data->set('catid', JRequest::getInt('catid', $app->getUserState(com_weblinks.weblink.'.filter.category_id')));
            }
        }

        return $data;
    }
    
    public function getItems(){
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($this->_tableName));
        $db->setQuery($query);
        $this->_items = $db->loadObjectList();
        return $this->_items;
    }
    
    public function getItem($id){
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($this->_tableName));
        $query->where('id='.(int)$id);
        $db->setQuery($query);
        $item = $db->loadObject();
        if($item === null){
            JError::raiseError(500, 'Item '.$id.' Not found');
        }else{
            return $item;
        }
    }
    
    function getNewItem(){
        $newItem =& $this->getTable( $this->_tableClassName );
        $newItem->id = 0;
        return $newItem;
    }
    
    public function store($data){
        $table =& $this->getTable();
        $table->reset();
        if(!$table->bind($data)){
            $this->setError($table->getError());
            return false;
        }
        if(!$table->check()){
            $this->setError($table->getError());
            return false;
        }
        if(!$table->store()){
            $this->setError($table->getError());
            return false;
        }
        return true;
    }
    
    public function delete($cids){
        $cids = array_map('intval', $cids);
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName($this->_tableName));
        $query->where('id IN ('.implode(',', $cids).')');
        $db->setQuery($query);
        if( !$db->query() ){
            $errorMsg = $this->getDBO()->getErrorMsg();
            JError::raiseError(500, 'Error deleting: '.$errorMsg);
        }
    }
    
    private function _setPublish($cids, $state){
        $cids = array_map('intval', $cids);
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->quoteName($this->_tableName));
        $query->set('published = '.(int)$state);
        $query->where('id IN ('.implode(',', $cids).')');
        $db->setQuery($query);
        if( !$db->query() ){
            $errorMsg = $this->getDBO()->getErrorMsg();
            JError::raiseError(500, 'Error Setting publish state: '.$errorMsg);
        }
    }
    
    public function unpublish($cids){
        $this->_setPublish($cids, 0);
    }
    
    public function publish($cids){
        $this->_setPublish($cids, 1);
    }
}
