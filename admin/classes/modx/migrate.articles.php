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

class MigrateArticles extends Migrate{
    
    protected static $_sourceContentTableName = '#__content';
    protected static $_sourceCategoryTableName = '#__categories_relations';
    protected static $_destContentTableName = '#__content';
    protected static $_destAssetsTableName = '#__assets';


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);

    }
    
    public function getContent(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select(
            '*'
        );
        $query->from($db->nameQuote(self::$_sourceContentTableName));
        $query->where('zone_type = 1');
        $query->where('zone LIKE '.$db->Quote('center_col%'));
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    } 
    
    public function clearData(){
        $isSuccessful = true;
        return (bool)$isSuccessful;
    }
    
    public function hasNoConflict(){
        return true;
    }
    
    protected function titleFromHTML($source){
        $html = new DOMDocument();
        $html->recover = true;
        $html->strictErrorChecking = false;
        $html->loadHTML($source);
        foreach($html->getElementsByTagName('p') as $p) {
            if($p->getAttribute('class') == 'rubrikBlueDotted'){
                return $p->nodeValue;
            }
            
        }
        return $title;
    }
    
    protected function setContent($id, $source, $catID, $assetId){
        $title = strip_tags($this->titleFromHTML($source));
        $alias = JFilterOutput::stringURLSafe($title);
        $cleanedSource = strip_tags($source, '<p><a><img><br><h2><h3><h4><ul><li><strong><b><i><u><table><tr><td>');
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_destContentTableName));
        $query->set('id = '.(int)$id);
        $query->set('asset_id = '.(int)$assetId);
        $query->set('title = '.$db->Quote($title));
        $query->set('alias = '.$db->Quote($alias));
        $query->set('introtext = '.$db->Quote($cleanedSource));
        $query->set('state = 1');
        $query->set('catid = '.(int)$catID);
        $date =& JFactory::getDate();
        $query->set('created = '.$db->quote($date->toMySQL()));
        $query->set('created_by = 42');
        $query->set('images = '.$db->Quote('{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}'));
        $query->set('urls = '.$db->Quote('{"urla":null,"urlatext":"","targeta":"","urlb":null,"urlbtext":"","targetb":"","urlc":null,"urlctext":"","targetc":""}'));
        $query->set('attribs = '.$db->Quote('{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}'));
        $query->set('metadata = '.$db->Quote('{"robots":"","author":"","rights":"","xreference":""}'));
        $query->set('language = '.$db->Quote('*'));
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting category '.$catId);
            return false;
        }
        return true;
    }
    
    public function getNextRGT(){
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->nameQuote(self::$_destAssetsTableName));
        $query->where('zone LIKE '.$db->Quote('center_col%'));
        $query->order('rgt DESC');
        $db->setQuery($query);
        $retult = $db->loadObject();
        return $retult->rgt + 1;
    } 
    
    public function getCategory($contentID){
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select(
            '*'
        );
        $query->from($db->nameQuote(self::$_sourceCategoryTableName));
        $query->where('item_id = '.(int)$contentID);
        $db->setQuery($query);
        $result = $db->loadObject();
        $catid = $result->cid;
        if($catid == 0){
            $catid = 2;
        }
        return $catid;
    } 
    
    protected function setAsset($contentId, $catId, $name){
        $nextRGT = $this->getNextRGT();
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_destAssetsTableName));
        $query->set('parent_id = '.(int)$catId);
        $query->set('lft = '.(int)$nextRGT);
        $query->set('rgt = '.(int)$nextRGT+1);
        $query->set('level = '.(int)3);
        $query->set('name = '.$db->Quote('com_content.article.'.(int)$contentId));
        $query->set('title = '.$db->Quote($name));
        $query->set('rules = '.$db->Quote('{"core.delete":[],"core.edit":[],"core.edit.state":[]}'));
        $db->setQuery($query);
        $result = $db->query();
        $lastId = $db->insertid();
        return $lastId;
    }
    
    public function migrateArticles(){
        $isSuccessful = true;
        $contents = $this->getContent();
        foreach ($contents as $content) {
            $catID = $this->getCategory($content->id);
            $name = strip_tags($this->titleFromHTML($content->data));
            $assetID = $this->setAsset($content->id, $catID, $name);
            $isSuccessful *= $this->setContent($content->id, $content->data, $catID, $assetID);
        }
        return (bool)$isSuccessful;
    }
        

    public function __set($name, $value) {
        $this->$name = $value;
    }

}
