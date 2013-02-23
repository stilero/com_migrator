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

// no direct access
defined('_JEXEC') or die('Restricted access'); 

// import joomla controller library
jimport('joomla.application.component.controller');

class ShopMigratorControllerMigration extends JController{
    
    public static $modelName = 'migration';
    public static $viewName = 'migration';
    
    public function display(){
        //Set Default View and Model
        $view =& $this->getView( self::$viewName, 'raw' );
        $model =& $this->getModel(  self::$modelName );
//        $systemId = JRequest::getInt('systemid');
//        $tasks = $model->getTasks(1);
//        $view->assignRef('tasks', $tasks);
        $view->setModel( $model, true );
        $view->display();
    }
    
    public function edit(){
        $cids = JRequest::getVar('cid', null, 'default', 'array');
        if( $cids === null ){
            JError::raiseError( 500,
            'cid parameter missing from the request' );
        }
        $migrationId = (int)$cids[0];
        $view =& $this->getView( self::$viewName, 'html' );
        $layout =& $this->getLayout( 'form', 'html' );
        $model =& $this->getModel( self::$modelName );
        $view->setModel( $model, true );
        $view->setLayout('edit');
        $view->edit( $migrationId );
    }
    
    function add(){
        $view =& $this->getView( self::$viewName, 'html' );
        $layout =& $this->getLayout( 'form', 'html' );
        $model =& $this->getModel( self::$modelName );
        $view->setModel( $model, true );
        $view->setLayout('edit');
        $view->add();
    }
    
    function save(){
        $this->apply();
        $redirectTo = 'index.php?option='.JRequest::getVar('option').'&task=display&view='.JRequest::getVar('view');
        $this->setRedirect( $redirectTo, 'Saved' );
    }
    
    function apply(){
        $model =& $this->getModel( self::$modelName );
        $model->store();
    }
    
    function cancel(){
        $redirectTo = 'index.php?option='.JRequest::getVar('option').'&task=display&view='.JRequest::getVar('view');
        $this->setRedirect( $redirectTo, 'Cancelled' );
    }
    
    function remove(){
        $cids = JRequest::getVar('cid', null, 'default', 'array');
        if( $cids === null ){
            JError::raiseError( 500, 'Nothing were selected for removal' );
        }
        $model =& $this->getModel( self::$modelName);
        $model->delete( $cids);
        $redirectTo = 'index.php?option='.JRequest::getVar( 'option' ).'&task=display&view='.JRequest::getVar('view');
        $this->setRedirect( $redirectTo, 'Deleted' );
    }
}
