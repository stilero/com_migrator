<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-09 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class Migrate{
    
    protected $_srcDB;
    protected $_storeid;
    protected $_storeUrl;
    protected $_sourceDB;
    protected $_destDB;
    protected $_sourceData;
    protected $_destinationData;
    protected $_error;
    
    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        $this->_sourceDB =& $MigrateSrcDB;
        $this->_destDB =& $MigrateDestDB;
        $this->_storeid = $storeid;
        $this->_storeUrl = $storeUrl;
    }
    
    public function migrateFile($srcFile, $destPath='images/stories/virtuemart/'){
        
        $destFile = JPATH_ROOT.DS.$destPath.JFile::getName($srcFile);
        $tmpFile = JPATH_ROOT.DS.'tmp'.DS.JFile::getName($srcFile);
        $content = file_get_contents($srcFile);
        file_put_contents($tmpFile, $content);
        JFile::move($tmpFile, $destFile);
        if( ! JFile::exists($destFile)){
            $this->setError(MigrateError::FILE_MOVE_PROBLEM, 'Could not move image '.$srcFile);
            return false;
        }
        return $destFile;
    }
    
    public function resizeImage($bigImagePath, $height, $width, $savePath){
        $image = new JImage($bigImagePath);
        $resized = $image->resize($width, $height, true, JImage::SCALE_INSIDE);
        $resized->toFile($savePath);
    }
    
    public function setError($code, $message){
        $this->_error = array(
            'code' => (int)$code,
            'message' => $message
        );
    }
    
    public function getError(){
        return $this->_error;
    }
    
    public function __get($name) {
        return $this->$name;
    }
    
}
