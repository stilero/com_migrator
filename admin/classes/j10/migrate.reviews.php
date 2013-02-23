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
jimport( 'joomla.html.html.string' );
JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
class MigrateReviews extends Migrate{
    
    protected static $_reviewTable = '#__review';
    protected static $_vmRatingsTable = '#__virtuemart_ratings';
    protected static $_vmRatingReviewsTable = '#__virtuemart_rating_reviews';
    protected static $_vmRatingVotesTable = '#__virtuemart_rating_votes';

    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);
    }
    
    public function getData(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from(self::$_reviewTable);
        $db->setQuery($query);
        $this->_sourceData = $db->loadObjectList();
        return $this->_sourceData;
    }
    
    public function clearData(){
        $isSuccessful = true;
        $tables = array(
            self::$_vmRatingsTable,
            self::$_vmRatingReviewsTable,
            self::$_vmRatingVotesTable
        );
        $db =& $this->_destDB;
        foreach ($tables as $table) {
            $query = $db->getQuery(true);
            $query->delete($table);
            $db->setQuery($query);
            $result = $db->query();
            if(!$result){
               $isSuccessful *= false;
            }
        }
        return (bool)$isSuccessful;
    }
    
    protected function setRating($review){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmRatingsTable);
        $query->set('virtuemart_product_id = '.(int)$review->product_id);
        $query->set('rates = '.(int)$review->rating);
        $query->set('ratingcount = 1');
        $query->set('rating = '.floatval($review->rating));
        $query->set('published = '.(int)$review->status);
        $query->set('created_on = '.$db->quote($review->date_added));
        $query->set('created_by = '.(int)$review->customer_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed inserting Rating for product:'.$review->product_id);
            return false;
        }
        return true;
    }
    
    protected function setRatingReview($review){
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert(self::$_vmRatingReviewsTable);
        $query->set('virtuemart_product_id = '.(int)$review->product_id);
        $query->set('comment = '.$db->quote(JHtmlString::truncate(html_entity_decode($review->text), 2000, true, false)));
        $query->set('review_ok = '.(int)$review->status);
        $query->set('review_rates = '.(int)$review->rating);
        $query->set('review_ratingcount = 1');
        $query->set('review_rating = '.floatval($review->rating));
        $query->set('review_editable = 0');
        $query->set('created_on = '.$db->quote($review->date_added));
        $query->set('created_by = '.(int)$review->customer_id);
        $db->setQuery($query);
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting Rating Review for product:'.$review->product_id);
            return false;
        }
        return true;
    }
    
    public function migrateReviews(){
        $isSuccessful = true;
        $reviews = $this->getData();
        foreach ($reviews as $review) {
            $isSuccessful *= $this->setRating($review);
            $isSuccessful *= $this->setRatingReview($review);
        }
        return (bool)$isSuccessful;
    }
}
