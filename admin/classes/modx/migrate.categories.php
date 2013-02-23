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
jimport( 'joomla.filter.filteroutput' );
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');

class MigrateCategories extends Migrate{
    
    protected static $_sourceCategoriesTableName = '#__categories';
    protected static $_sourceContentTableName = '#__content';
    protected static $_sourceCategoriesRelationsTableName = '#__categories_relations';
    protected static $_destCategoriesTableName = '#__categories';
    protected static $_destAssetsTableName = '#__assets';
    
    /*
    protected static $_catDescTable = '#__category_description';
    protected static $_toCategoriesTableName = '#__virtuemart_categories';
    protected static $_vmMediasTable = '#__virtuemart_medias';
    protected static $_vmCatMediaTable = '#__virtuemart_category_medias';
    protected static $_vmCatDescTable = '#__virtuemart_categories_en_gb';
    protected static $_vmCatCatsTable = '#__virtuemart_category_categories';
    protected static $destImagesFolder = 'images/stories/virtuemart/category/';
    protected static $destImagesThumbFolder = 'images/stories/virtuemart/category/resized/';
    protected  $srcImagesFolderURL;
    protected $_vmImagePath;
    protected $_vmThumbPath;
    protected $_thumbWidth;
    protected $_thumbHeight;
     */


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
    }
    
    
    public function getCategories(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select(
            'cat.*'
        );
        $query->from($db->nameQuote(self::$_sourceCategoriesTableName).' cat');
        $query->where('cat.siteid = 1');
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    } 
    
    public function clearData(){
        $isSuccessful = true;
//        $tables = array(
//            self::$_toCategoriesTableName,
//            self::$_vmCatMediaTable,
//            self::$_vmCatDescTable
//        );
//        $db =& $this->_destDB;
//        foreach ($tables as $table) {
//            $query = $db->getQuery(true);
//            $query->delete($table);
//            $query->where('virtuemart_category_id > 5');
//            $db->setQuery($query);
//            $result = $db->query();
//            if(!$result){
//               $isSuccessful *= false;
//            }
//        }
//        $query =& $db->getQuery(true);
//        $query->delete(self::$_vmCatCatsTable);
//        $query->where('category_child_id > 5' );
//        $db->setQuery($query);
//        $result = $db->query();
//        if(!$result){
//           $isSuccessful *= false;
//        }
        return (bool)$isSuccessful;
    }
    
    public function hasNoConflict(){
//        $db =& $this->_sourceDB;
//        $query = $db->getQuery(true);
//        $query->select('category_id');
//        $query->from($db->nameQuote(self::$_sourceCategoriesRelationsTableName));
//        $query->where('store_id = '.(int)$this->_storeid);
//        $db->setQuery($query);
//        $srcIds = $db->loadResultArray();
//        $db2 =& $this->_destDB;
//        $query2 = $db2->getQuery(true);
//        $query2->select('virtuemart_category_id');
//        $query2->from($db2->nameQuote(self::$_toCategoriesTableName));
//        $query2->where('virtuemart_category_id IN ('.implode(',', $srcIds).')');
//        $db2->setQuery($query2);
//        $result = $db2->loadResultArray();
//        if($result){
//            $this->setError(MigrateError::DB_ERROR, 'Conflict detected. Categories already exists with ID: '.implode(', ', $result));
//            return false;
//        }
        return true;
    }
    
    protected function setDescription($desc){
        $isSuccessful = true;
        $slug = strtolower(str_replace(' ', '', $desc->name));
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_vmCatDescTable));
        $query->set('virtuemart_category_id = '.(int)$desc->category_id);
        $query->set('category_name = '.$db->quote($desc->name));
        $query->set('category_description = '.$db->quote($desc->description));
        $query->set('metadesc = '.$db->quote($desc->meta_description));
        $query->set('metakey = '.$db->quote($desc->meta_keyword));
        $query->set('slug = '.$db->quote($slug));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting category description'.$desc->category_id);
            $isSuccessful = false;
        }
        return $isSuccessful;
    }
    
    public function migrateDescriptions(){
        $isSuccessful = true;
        $descs = $this->getCategoriesForStore();
        foreach ($descs as $desc) {
            $isSuccessful *= $this->setDescription($desc);
        }
        return (bool)$isSuccessful;
    }
    protected function getParentAssetId($name = 'com_content'){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->select(
            '*'
        );
        $query->from($db->nameQuote(self::$_destAssetsTableName));
        $query->where('name = '.$db->Quote($name));
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result->id;
    }
    
    protected function setAsset($catId, $parentId, $name){
        $parentAsset = $this->getParentAssetId();
        $level = ($parentId == 0)? 2:3;
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_destAssetsTableName));
        $query->set('parent_id = '.(int)$parentAsset);
        $query->set('level = '.(int)$level);
        $query->set('name = '.$db->Quote('com_content.category.'.(int)$catId));
        $query->set('title = '.$db->Quote($name));
        $query->set('rules = '.$db->Quote('{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}'));
        $db->setQuery($query);
        $result = $db->query();
        $lastId = $db->insertid();

        return $lastId;
    }
    
    
    protected function setCategory($catId, $parentId, $name, $assetId){
        $level = ($parentId == 0)? 1:2;
        $parent = ($parentId == 0)? 1:$parentId;
        $alias = JFilterOutput::stringURLSafe($name);
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_destCategoriesTableName));
        $query->set('id = '.(int)$catId);
        $query->set('asset_id = '.(int)$assetId);
        $query->set('parent_id = '.(int)$parent);
        $query->set('level = '.(int)$level);
        $query->set('extension = \'com_content\'');
        $query->set('title = '.$db->Quote($name));
        $query->set('path = '.$db->Quote($alias));
        $query->set('alias = '.$db->Quote($alias));
        $query->set('published = 1');
        $query->set('access = 1');
        $query->set('params = '.$db->Quote('{"category_layout":"","image":""}'));
        $query->set('metadata = '.$db->Quote('{"author":"","robots":""}'));
        $query->set('created_user_id = 42');
        $date =& JFactory::getDate();
        $query->set('created_time = '.$db->quote($date->toMySQL()));
        $query->set('language = '.$db->Quote('*'));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting category '.$catId);
            return false;
        }
        return true;
    }
    
    public function migrateCategories(){
        $isSuccessful = true;
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $assetID = $this->setAsset($category->id, $category->parent, $category->name);
            $isSuccessful *= $this->setCategory($category->id, $category->parent, $category->name, $assetID);
        }
        return (bool)$isSuccessful;
    }
        
//    protected function setCategoryCategories($catId, $parentId){
//        $db =& $this->_destDB;
//        $query = $db->getQuery(true);
//        $query->insert($db->nameQuote(self::$_vmCatCatsTable));
//        $query->set('category_parent_id = '.(int)$parentId);
//        $query->set('category_child_id = '.(int)$catId);
//        $db->setQuery($query);
//        $result = $db->query();
//        if(!$result){
//            $this->setError(MigrateError::DB_ERROR, 'Failed setting Category Categories'.$catId);
//            return false;
//        }
//        return true;
//    }
//    
//    public function migrateCategoryCategories(){
//        $isSuccessful = true;
//        $ocCategories = $this->getCategoriesForStore();
//        foreach ($ocCategories as $ocCategory) {
//            $isSuccessful *= $this->setCategoryCategories($ocCategory->category_id, $ocCategory->parent_id);
//        }
//        return (bool)$isSuccessful;
//    }
//    
//    protected function setImage($catID, $bigImage, $thumbImage){
//        $isSuccessful = true;
//        $file_title = str_replace('.'.JFile::getExt($bigImage), '', JFile::getName($bigImage));
//        $imgprop = JImage::getImageFileProperties($bigImage);
//        $mime_type = $imgprop->type;
//        $shortPath = str_replace(JPATH_ROOT.DS, '', $bigImage);
//        $db =& $this->_destDB;
//        $query = $db->getQuery(true);
//        $query->insert($db->nameQuote(self::$_vmMediasTable));
//        $query->set('virtuemart_vendor_id = 1');
//        $query->set('file_title = '.$db->quote($file_title));
//        $query->set('file_meta = '.$db->quote($file_title));
//        $query->set('file_mimetype = '.$db->quote('image/jpeg'));
//        $query->set('file_type = '.$db->quote('category'));
//        $query->set('file_url = '.$db->quote($shortPath));
//        $query->set('file_url_thumb = '.$db->quote($thumbImage));
//        $db->setQuery($query);
//        $result = $db->query();
//        if(!$result){
//            $isSuccessful = false;
//            $this->setError(MigrateError::DB_ERROR, 'Failed Setting image for Cateogry '.$catId);
//        }
//        $lastRowId = $db->insertid();
//        $query2 = $db->getQuery(true);
//        $query2->insert($db->nameQuote(self::$_vmCatMediaTable));
//        $query2->set('virtuemart_category_id = '.(int)$catID);
//        $query2->set('virtuemart_media_id = '.(int)$lastRowId);
//        $db->setQuery($query2);
//        $result = $db->query();
//        if(!$result){
//            $this->setError(MigrateError::DB_ERROR, 'Failed setting image for Category in media '.$catId);
//            $isSuccessful *= false;
//        }
//        return (bool)$isSuccessful;
//    }
//    
//    public function migrateImages(){
//        $isSuccessful = true;
//        $images = $this->getCategoriesForStore();
//        foreach ($images as $image) {
//            if($image->image != ''){
//                $bigImage = $this->migrateFile($image->imageurl, $this->_vmImagePath);
//                $thumbImage = $this->_vmThumbPath.JFile::getName($bigImage);
//                 $this->resizeImage($bigImage, $this->_thumbHeight, $this->_thumbWidth, JPATH_ROOT.DS.$thumbImage);
//                if($bigImage != FALSE){
//                    $isSuccessful *= $this->setImage($image->category_id, $bigImage, $thumbImage);
//                 }else{
//                     $$this->_error = array(MigrateError::FILE_MOVE_PROBLEM, $bigImage);
//                     $isSuccessful *= false;
//                 }
//            }
//        }
//        return (bool)$isSuccessful;
//    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

}
