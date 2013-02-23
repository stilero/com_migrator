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

class MigrateTaxes extends Migrate{
    
    protected static $_taxClass = '#__tax_class';
    protected static $_taxRate = '#__tax_rate';
    protected static $_taxRateToCustGroup = '#__tax_rate_to_customer_group';
    protected static $_taxRule = '#__tax_rule';
    protected static $_vmManuENGBTable = '#__virtuemart_manufacturers_en_gb';
    protected static $_vmManuTable = '#__virtuemart_manufacturers';
    protected static $_vmMediasTable = '#__virtuemart_medias';
    protected static $_vmManufacturerMediasTable = '#__virtuemart_manufacturer_medias';
    protected static $destImagesFolder = 'images/stories/virtuemart/manufacturer/';
    protected static $destImagesThumbFolder = 'images/stories/virtuemart/manufacturer/resized/';


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
        $this->srcImagesFolderURL = $storeUrl.'image/';
    }
    
    public function getData(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('tc.*, tr.tax_rate_id, tr.name, tr.rate, tr.type');
        $query->from(self::$_taxRule.' tru');
        $query->innerJoin(self::$_taxClass.' tc ON tc.tax_class_id = tru.tax_class_id');
        $query->innerJoin(self::$_taxRate.' tr ON tr.tax_rate_id = tru.tax_rate_id');
        print $query->dump();
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    protected function setManufacturer($id, $name){
        $slug = strtolower(str_replace(' ', '', $name));
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmManuENGBTable);
        $query->set('virtuemart_manufacturer_id = '.(int)$id);
        $query->set('mf_name = '.$db->quote($name));
        $query->set('slug = '.$db->quote($slug));
        $db->setQuery($query);
        $db->query();
        $query = $db->getQuery(true);
        $query->insert(self::$_vmManuTable);
        $query->set('virtuemart_manufacturer_id = '.(int)$id);
        $query->set('virtuemart_manufacturercategories_id = 1');
        $db->setQuery($query);
        $db->query();
    }
    
    public function migrateCalcs(){
        $manufacturers = $this->getData();
        foreach ($manufacturers as $manufacturer) {
            $this->setManufacturer($manufacturer->manufacturer_id, $manufacturer->name);
        }
    }
    
    protected function setImage($id, $bigImage, $thumbImage){
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
            $this->_error[] = array(MigrateError::DB_ERROR, 'Failed Setting image for Manufacturer '.$id);
        }
        $lastRowId = $db->insertid();
        $query2 = $db->getQuery(true);
        $query2->insert(self::$_vmManufacturerMediasTable);
        $query2->set('virtuemart_manufacturer_id = '.(int)$id);
        $query2->set('virtuemart_media_id = '.(int)$lastRowId);
        $db->setQuery($query2);
        $result = $db->query();
        if(!$result){
            $this->_error[] = array(MigrateError::DB_ERROR, 'Failed setting image for Manufacturer in media '.$catId);
        }
    }
    
    public function migrateImages(){
        $images = $this->getData();
        foreach ($images as $image) {
            if($image->image != ''){
                $bigImage = $this->migrateFile($image->imageurl, self::$destImagesFolder);
                $thumbImage = self::$destImagesThumbFolder.JFile::getName($bigImage);
                 $this->resizeImage($bigImage, 90, 90, JPATH_BASE.DS.$thumbImage);
                if($bigImage != FALSE){
                    $this->setImage($image->manufacturer_id, $bigImage, $thumbImage);
                 }else{
                     $this->error[] = array(MigrateError::FILE_MOVE_PROBLEM => $bigImage);
                 }
            }
        }
        return true;
    }
}
