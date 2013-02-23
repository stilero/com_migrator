<?php
/**
 * ShopMigrator
 *
 * @version  1.0
 * @package Stilero
 * @subpackage ShopMigrator
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-14 Stilero Webdesign, Stilero AB
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class MigrateManufacturer extends Migrate{
    
    protected static $_manuTable = '#__manufacturer';
    protected static $_manuToStoreTable = '#__manufacturer_to_store';
    protected static $_vmManuENGBTable = '#__virtuemart_manufacturers_en_gb';
    protected static $_vmManuTable = '#__virtuemart_manufacturers';
    protected static $_vmMediasTable = '#__virtuemart_medias';
    protected static $_vmManufacturerMediasTable = '#__virtuemart_manufacturer_medias';
    protected static $destImagesFolder = 'images/stories/virtuemart/manufacturer/';
    protected static $destImagesThumbFolder = 'images/stories/virtuemart/manufacturer/resized/';
    protected  $srcImagesFolderURL;
    protected $_vmImagePath;
    protected $_vmThumbPath;
    protected $_thumbWidth;
    protected $_thumbHeight;

    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
        $this->srcImagesFolderURL = $storeUrl.'image/';
        $this->_vmImagePath = self::$destImagesFolder;
        $this->_vmThumbPath = self::$destImagesThumbFolder;
        $this->_thumbHeight = 90;
        $this->_thumbWidth = 90;
    }
    
    public function setImageFolder($folderPathRelativeToJoomlaRoot){
        $this->_vmImagePath = $folderPathRelativeToJoomlaRoot;
        $this->_vmThumbPath = $folderPathRelativeToJoomlaRoot.'resized/';
    }
    
    public function setThumbSize($width, $height){
        $this->_thumbHeight = $height;
        $this->_thumbWidth = $width;
    }
    
    public function getData(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('m.*, CONCAT(\''.$this->srcImagesFolderURL.'\', m.image) AS \'imageurl\'');
        $query->from(self::$_manuToStoreTable.' mts');
        $query->innerJoin(self::$_manuTable.' m ON m.manufacturer_id = mts.manufacturer_id');
        $query->where('mts.store_id = '.(int)$this->_storeid);
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function clearData(){
        $isSuccessful = true;
        $tables = array(
            self::$_vmManuENGBTable,
            self::$_vmManuTable,
            self::$_vmManufacturerMediasTable
        );
        $db =& $this->_destDB;
        foreach ($tables as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $query->where('virtuemart_manufacturer_id > 1');
            $db->setQuery($query);
            $result = $db->query();
            if(!$result){
               $isSuccessful *= false;
            }
        }
        return (bool)$isSuccessful;
    }
    
    public function hasNoConflict(){
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('manufacturer_id');
        $query->from($db->nameQuote(self::$_manuToStoreTable));
        $query->where('store_id = '.(int)$this->_storeid);
        $db->setQuery($query);
        $srcIds = $db->loadResultArray();
        $db2 =& $this->_destDB;
        $query2 = $db2->getQuery(true);
        $query2->select('virtuemart_manufacturer_id');
        $query2->from($db2->nameQuote(self::$_vmManuTable));
        $query2->where('virtuemart_manufacturer_id IN ('.implode(',', $srcIds).')');
        $db2->setQuery($query2);
        $result = $db2->loadResultArray();
        if($result){
            $this->setError(MigrateError::DB_ERROR, 'Conflict detected. Manufacturer already exists with ID: '.implode(', ', $result));
            return false;
        }
        return true;
    }
    
    protected function setManufacturer($id, $name){
        $isSuccessful = true;
        $slug = strtolower(str_replace(' ', '', $name));
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmManuENGBTable);
        $query->set('virtuemart_manufacturer_id = '.(int)$id);
        $query->set('mf_name = '.$db->quote($name));
        $query->set('slug = '.$db->quote($slug));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
           $isSuccessful *= false;
        }
        $query = $db->getQuery(true);
        $query->insert(self::$_vmManuTable);
        $query->set('virtuemart_manufacturer_id = '.(int)$id);
        $query->set('virtuemart_manufacturercategories_id = 1');
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
           $isSuccessful *= false;
           $this->setError(MigrateError::DB_ERROR, 'Failed saving manufacturer '.$id.' to DB');
        }
        return (bool)$isSuccessful;
    }
    
    public function migrateManufacturers(){
        $isSuccessful = true;
        $manufacturers = $this->getData();
        foreach ($manufacturers as $manufacturer) {
            $isSuccessful *= $this->setManufacturer($manufacturer->manufacturer_id, $manufacturer->name);
        }
        return (bool)$isSuccessful;
    }
    
    protected function setImage($id, $bigImage, $thumbImage){
        $isSuccessful = true;
        $file_title = str_replace('.'.JFile::getExt($bigImage), '', JFile::getName($bigImage));
        $imgprop = JImage::getImageFileProperties($bigImage);
        $mime_type = $imgprop->type;
        $shortPath = str_replace(JPATH_ROOT.DS, '', $bigImage);
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmMediasTable);
        $query->set('virtuemart_vendor_id = 1');
        $query->set('file_title = '.$db->quote($file_title));
        $query->set('file_meta = '.$db->quote($file_title));
        $query->set('file_mimetype = '.$db->quote('image/jpeg'));
        $query->set('file_type = '.$db->quote('manufacturer'));
        $query->set('file_url = '.$db->quote($shortPath));
        $query->set('file_url_thumb = '.$db->quote($thumbImage));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed saving image for manufacturer '.$id);
            $isSuccessful = false;
        }
        $lastRowId = $db->insertid();
        $query2 = $db->getQuery(true);
        $query2->insert(self::$_vmManufacturerMediasTable);
        $query2->set('virtuemart_manufacturer_id = '.(int)$id);
        $query2->set('virtuemart_media_id = '.(int)$lastRowId);
        $db->setQuery($query2);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed inserting image for manufacturer '.$id.' to DB');
            $isSuccessful *= false;
        }
        return (bool)$isSuccessful;
    }
    
    public function migrateImages(){
        $isSuccessful = true;
        $images = $this->getData();
        foreach ($images as $image) {
            if($image->image != ''){
                $bigImage = $this->migrateFile($image->imageurl, $this->_vmImagePath);
                $thumbImage = $this->_vmThumbPath.JFile::getName($bigImage);
                 $this->resizeImage($bigImage, $this->_thumbHeight, $this->_thumbWidth, JPATH_ROOT.DS.$thumbImage);
                if($bigImage != FALSE){
                    $isSuccessful *= $this->setImage($image->manufacturer_id, $bigImage, $thumbImage);
                 }else{
                     $this->setError(MigrateError::FILE_MOVE_PROBLEM, 'Failed moving image '.$image->imageurl);
                     $isSuccessful *= false;
                 }
            }
        }
        return (bool)$isSuccessful;
    }
}
