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

// import joomla controller library
jimport('joomla.application.component.controller');

class ShopMigratorControllerDatabases extends JController{
    
    public static $modelName = 'databases';
    public static $viewName = 'databases';
    
    public function display(){
        //Set Default View and Model
        $view =& $this->getView( self::$viewName, 'html' );
        $model =& $this->getModel(  self::$modelName );
        $view->setModel( $model, true );
        $view->display();
    }
    
    public function edit(){
        $cids = JRequest::getVar('cid', null, 'default', 'array');
        if( $cids === null ){
            JError::raiseError( 500,
            'cid parameter missing from the request' );
        }
        $databasesId = (int)$cids[0];
        $view =& $this->getView( self::$viewName, 'html' );
        $layout =& $this->getLayout( 'form', 'html' );
        $model =& $this->getModel( self::$modelName );
        $view->setModel( $model, true );
        $view->setLayout('edit');
        $view->edit( $databasesId );
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
