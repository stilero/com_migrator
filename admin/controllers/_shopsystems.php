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
 * This file is part of shopsystems.
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

// import joomla controller library
jimport('joomla.application.component.controller');

class ShopMigratorControllerShopsystems extends JController{
    
    public static $modelName = 'shopsystems';
    public static $viewName = 'shopsystems';
    
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
        $shopsystemsId = (int)$cids[0];
        $view =& $this->getView( self::$viewName, 'html' );
        $model =& $this->getModel( self::$modelName );
        $view->setModel( $model, true );
        $view->setLayout('edit');
        $view->edit( $shopsystemsId );
    }
    
    function add(){
        $view =& $this->getView( self::$viewName, 'html' );
        $model =& $this->getModel( self::$modelName );
        $view->setModel( $model, true );
        $view->setLayout('edit');
        $view->add();
    }
    
    function save(){
        $wasSuccessful = $this->apply();
        $redirectMessage = 'Saved';
        $redirectMessageType = '';
        if(!$wasSuccessful){
            $redirectMessage = 'Failed Saving';
            $redirectMessageType = 'error';
        }
        $redirectTo = 'index.php?option='.JRequest::getVar('option').'&task=display&view='.JRequest::getVar('view');
        $this->setRedirect( $redirectTo, $redirectMessage, $redirectMessageType );
    }
    
    function apply(){
        $model =& $this->getModel( self::$modelName );
        $data = JRequest::get('post');
        $wasSuccessful = $model->store($data);
        if(!$wasSuccessful){
            $app =& JFactory::getApplication();
            $error = $model->getError();
            $app->enqueueMessage($error, 'error');
            return false;
        }
        return true;
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
