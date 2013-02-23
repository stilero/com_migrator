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
jimport( 'joomla.html.html.string' );
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
class MigrateProducts extends Migrate{
    
    protected static $_prodTable = '#__product';
    protected static $_prodToStoreTable = '#__product_to_store';
    protected static $_prodDescTable = '#__product_description';
    protected static $_prodImageTable = '#__product_image';
    protected static $_prodRelatedTable = '#__product_related';
    protected static $_prodLengthClassTable = '#__length_class_description';
    protected static $_prodWeightClassTable = '#__weight_class_description';
    protected static $_prodToCatTable = '#__product_to_category';
    protected static $_vmProductTable = '#__virtuemart_products';
    protected static $_vmProductDescTable = '#__virtuemart_products_en_gb';
    protected static $_vmProductCatTable = '#__virtuemart_product_categories';
    protected static $_vmProductPriceTable = '#__virtuemart_product_prices';
    protected static $_vmProductMediasTable = '#__virtuemart_product_medias';
    protected static $_vmMediasTable = '#__virtuemart_medias';
    protected static $_vmProdRelationsTable = '#__virtuemart_product_relations';
    protected static $_vmProdCustomFieldsTable = '#__virtuemart_product_customfields';
    protected static $_vmProdManufacturerTable = '#__virtuemart_product_manufacturers';
    private $vmCurrencyCode;
    protected  $srcImagesFolderURL;
    protected static $destImagesFolder = 'images/stories/virtuemart/category/';
    protected static $destImagesThumbFolder = 'images/stories/virtuemart/category/resized/';
    protected $_vmImagePath;
    protected $_vmThumbPath;
    protected $_thumbWidth;
    protected $_thumbHeight;


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0, $vmCurrencyCode=144) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
        $this->srcImagesFolderURL = $storeUrl.'image/';
        $this->vmCurrencyCode = $vmCurrencyCode;
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
        $query->select('
            p.*, 
            dsc.name, 
            dsc.description, 
            dsc.meta_description, 
            dsc.meta_keyword, 
            dsc.tag, 
            l.unit AS length_uom, 
            w.unit AS weight_uom');
        $query->from(self::$_prodToStoreTable.' pts');
        $query->innerJoin(self::$_prodTable.' p ON p.product_id = pts.product_id');
        $query->innerJoin(self::$_prodDescTable.' dsc ON dsc.product_id = pts.product_id');
        $query->innerJoin(self::$_prodLengthClassTable.' l ON l.length_class_id = p.length_class_id');
        $query->innerJoin(self::$_prodWeightClassTable.' w ON w.weight_class_id = p.weight_class_id');
        $query->where('pts.store_id = '.(int)$this->_storeid);
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function getImages(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('i.*, CONCAT(\''.$this->srcImagesFolderURL.'\', i.image) AS \'imageurl\'');
        $query->from(self::$_prodImageTable.' i');
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function clearData(){
        $isSuccessful = true;
        $tables = array(
            self::$_vmProductTable,
            self::$_vmProdManufacturerTable,
            self::$_vmProdRelationsTable,
            self::$_vmProductDescTable,
            self::$_vmProductMediasTable,
            self::$_vmProductCatTable,
            self::$_vmProdCustomFieldsTable
        );
        $db =& $this->_destDB;
        foreach ($tables as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $query->where('virtuemart_product_id > 16');
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
        $query->select('product_id');
        $query->from($db->nameQuote(self::$_prodToStoreTable));
        $query->where('store_id = '.(int)$this->_storeid);
        $db->setQuery($query);
        $srcIds = $db->loadResultArray();
        $db2 =& $this->_destDB;
        $query2 = $db2->getQuery(true);
        $query2->select('virtuemart_product_id');
        $query2->from($db2->nameQuote(self::$_vmProductTable));
        $query2->where('virtuemart_product_id IN ('.implode(',', $srcIds).')');
        $db2->setQuery($query2);
        $result = $db2->loadResultArray();
        if($result){
            $this->setError(MigrateError::DB_ERROR, 'Conflict detected. Products already exists with ID: '.implode(', ', $result));
            return false;
        }
        return true;
    }
    
    protected function setProduct($product){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProductTable);
        $query->set('virtuemart_product_id = '.(int)$product->product_id);
        $query->set('virtuemart_vendor_id = 1');
        $query->set('product_sku = '.$db->quote($product->sku));
        $query->set('product_weight = '.$db->quote($product->weight)); //Check weight class
        $query->set('product_weight_uom = '.$db->quote(strtoupper($product->weight_uom))); //Check weight class
        $query->set('product_length = '.$db->quote($product->length));
        $query->set('product_lwh_uom = '.$db->quote(strtoupper($product->length_uom))); //Check weight class
        $query->set('product_width = '.$db->quote($product->width));
        $query->set('product_height = '.$db->quote($product->height)); //Check length class
        $query->set('product_in_stock = '.(int)$product->quantity);
        $date =& JFactory::getDate($product->date_available);
        $query->set('product_available_date = '.$db->quote($date->toMySQL()));
        $query->set('hits = '.$db->quote($product->viewed));
        $query->set('published = '.(int)$product->status);
        $query->set('created_on = '.$db->quote($product->date_added));
        $query->set('modified_on = '.$db->quote($product->date_modified));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed inserting product to DB. ID:'.$product->product_id);
            return false;
        }
        return true;
    }
    
    protected function setProductDescription($product){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProductDescTable);
        $query->set('virtuemart_product_id = '.(int)$product->product_id);
        $query->set('product_s_desc = '.$db->quote(JHtmlString::truncate(html_entity_decode($product->description), 160, true, false)));
        $query->set('product_desc = '.$db->quote(html_entity_decode($product->description)));
        $query->set('product_name = '.$db->quote($product->name));
        $query->set('metadesc = '.$db->quote($product->meta_description));
        $query->set('metakey = '.$db->quote($product->meta_keyword));
        $query->set('slug = '.$db->quote(str_replace(' ', '', $product->name)));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting description for product id:'.$product->product_id);
            return false;
        }
        return true;
    }
    
    protected function setProductPrice($product){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProductPriceTable);
        $query->set('virtuemart_product_id = '.(int)$product->product_id);
        $query->set('product_price = '.$db->quote($product->price));
        $query->set('product_currency = '.(int)$this->vmCurrencyCode);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting price for product id:'.$product->product_id);
            return false;
        }
        return true;
    }
    
    protected function setProductManufacturer($product){
        $manuId = (int)$product->manufacturer_id == 0 ? 1 : (int)$product->manufacturer_id;
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProdManufacturerTable);
        $query->set('virtuemart_product_id = '.(int)$product->product_id);
        $query->set('virtuemart_manufacturer_id = '.(int)$manuId);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting manufacturer for product id:'.$product->product_id);
            return false;
        }
        return true;
    }
    
    protected function setImage($prodId, $bigImage, $thumbImage){
        $isSuccessful = true;
        $file_title = str_replace('.'.JFile::getExt($bigImage), '', JFile::getName($bigImage));
        $imgprop = JImage::getImageFileProperties($bigImage);
        $shortPath = str_replace(JPATH_ROOT.DS, '', $bigImage);
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmMediasTable);
        $query->set('virtuemart_vendor_id = 1');
        $query->set('file_title = '.$db->quote($file_title));
        $query->set('file_meta = '.$db->quote($file_title));
        $query->set('file_mimetype = '.$db->quote('image/jpeg'));
        $query->set('file_type = '.$db->quote('product'));
        $query->set('file_url = '.$db->quote($shortPath));
        $query->set('file_url_thumb = '.$db->quote($thumbImage));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed Setting image for Product '.$prodId);
            $isSuccessful = false;
        }
        $lastRowId = $db->insertid();
        $query2 = $db->getQuery(true);
        $query2->insert(self::$_vmProductMediasTable);
        $query2->set('virtuemart_product_id = '.(int)$prodId);
        $query2->set('virtuemart_media_id = '.(int)$lastRowId);
        $db->setQuery($query2);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed inserting to DB image for Product in media '.$prodId);
            $isSuccessful *= false;
        }
        return (bool)$isSuccessful;
    }
    
    public function migrateImages(){
        $isSuccessful = true;
        $images = $this->getImages();
        foreach ($images as $image) {
            if($image->image != ''){
                $bigImage = $this->migrateFile($image->imageurl, $this->_vmImagePath);
                $thumbImage = $this->_vmThumbPath.JFile::getName($bigImage);
                 $this->resizeImage($bigImage, $this->_thumbHeight, $this->_thumbWidth, JPATH_ROOT.DS.$thumbImage);
                if($bigImage != FALSE){
                    $isSuccessful *= $this->setImage($image->product_id, $bigImage, $thumbImage);
                 }else{
                     $this->setError(MigrateError::FILE_MOVE_PROBLEM ,$bigImage);
                     $isSuccessful *= false;
                 }
            }
        }
        return (bool)$isSuccessful;
    }
        
    public function migrateProducts(){
        $isSuccessful = true;
        $products = $this->getData();
        foreach ($products as $product) {
            $isSuccessful *= $this->setProduct($product);
            $isSuccessful *= $this->setProductDescription($product);
            $isSuccessful *= $this->setProductPrice($product);
            $isSuccessful *= $this->setProductManufacturer($product);
        }
        return (bool)$isSuccessful;
    }
    
    protected function setRelative($relative){
        $isSuccessful = true;
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProdRelationsTable);
        $query->set('virtuemart_product_id = '.(int)$relative->product_id);
        $query->set('related_products = '.(int)$relative->related_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            return true;
        }
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProdCustomFieldsTable);
        $query->set('virtuemart_product_id = '.(int)$relative->product_id);
        $query->set('virtuemart_custom_id = 1');
        $query->set('custom_value = '.(int)$relative->related_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $isSuccessful *= false;
            $this->setError(MigrateError::DB_ERROR, 'Failed setting related products to product id:'.$relative->product_id);
        }
        return (bool)$isSuccessful;
    }


    public function migrateRelated(){
        $isSuccessful = true;
        $db =& $this->_sourceDB;
        $query =& $db->getQuery(true);
        $query->select('*');
        $query->from(self::$_prodRelatedTable);
        $db->setQuery($query);
        $relatives = $db->loadObjectList();
        foreach ($relatives as $relative) {
            $isSuccessful *= $this->setRelative($relative);
        }
        return (bool)$isSuccessful;
    }
    
    protected function setProductCategory($prodCat){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmProductCatTable);
        $query->set('virtuemart_product_id = '.(int)$prodCat->product_id);
        $query->set('virtuemart_category_id = '.(int)$prodCat->category_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting product category for product id:'.$prodCat->product_id);
            return false;
        }
        return true;
    }

    public function migrateProdCategories(){
        $isSuccessful = true;
        $db =& $this->_sourceDB;
        $query =& $db->getQuery(true);
        $query->select('*');
        $query->from(self::$_prodToCatTable);
        $db->setQuery($query);
        $prodCats = $db->loadObjectList();
        foreach ($prodCats as $prodCat) {
            $isSuccessful *= $this->setProductCategory($prodCat);
        }
        return (bool)$isSuccessful;
    }
    
    
    
   
}
