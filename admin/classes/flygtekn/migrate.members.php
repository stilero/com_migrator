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
JLoader::register('RSMembershipHelper', JPATH_ADMINISTRATOR.'/components/com_rsmembership/helpers/rsmembership.php');

class MigrateMembers extends Migrate{
    
    protected static $_sourceTable = '#__trollhaettan';
    protected static $_destTable = '#__rsmembership_transactions';


    public function __construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid=0) {
        parent::__construct($MigrateSrcDB, $MigrateDestDB, $storeUrl, $storeid);

    }
    
    public function getMembers(){
        if(isset($this->_sourceData)){
            return $this->_sourceData;
        }
        $db =& $this->_sourceDB;
        $query = $db->getQuery(true);
        $query->select( '*' );
        $query->from($db->nameQuote(self::$_sourceTable));
        $db->setQuery($query);
        $this->_sourceData = $db->loadAssocList();
        return $this->_sourceData;
    } 
    
    public function clearData(){
        $isSuccessful = true;
        return (bool)$isSuccessful;
    }
    
    public function hasNoConflict(){
        return true;
    }
    
//    protected function titleFromHTML($source){
//        $html = new DOMDocument();
//        $html->recover = true;
//        $html->strictErrorChecking = false;
//        $html->loadHTML($source);
//        foreach($html->getElementsByTagName('p') as $p) {
//            if($p->getAttribute('class') == 'rubrikBlueDotted'){
//                return $p->nodeValue;
//            }
//            
//        }
//        return $title;
//    }
    


    protected function setMember($email, $user_data, $date){
        $time = strtotime($this->cleanField($date));
        if($time == null || $time== '' || $time==0){
            $time = time();
        }
        $email = $this->cleanField($email);
        $db =& $this->_destDB;
        $query = $db->getQuery(true);
        $query->insert($db->nameQuote(self::$_destTable));
        $query->set('user_id = 0');
        $query->set('user_email = '.$db->Quote($email));
        $query->set('user_data = '.$db->Quote($user_data));
        $query->set('type = '.$db->Quote('new'));
        $query->set('params = '.$db->Quote('membership_id=1'));
        $query->set('date = '.$db->Quote($time));
        $query->set('ip = '.$db->Quote('::1'));
        $query->set('price = '.$db->Quote('100.00'));
        $query->set('coupon = '.$db->Quote(''));
        $query->set('currency = '.$db->Quote('SEK'));
        $query->set('hash = '.$db->Quote(''));
        $query->set('custom = '.$db->Quote(''));
        $query->set('gateway = '.$db->Quote('Wire Transfer'));
        $query->set('status = '.$db->Quote('pending'));
        $query->set('response_log = '.$db->Quote(''));
        $db->setQuery($query);
        //echo $query->dump();exit;
        $result = $db->query();
        if(!$result){
            $this->setError(MigrateError::DB_ERROR, 'Failed setting category '.$catId);
            return false;
        }
        return true;
    }
//    
//    public function getNextRGT(){
//        $db =& $this->_sourceDB;
//        $query = $db->getQuery(true);
//        $query->select('*');
//        $query->from($db->nameQuote(self::$_destAssetsTableName));
//        $query->where('zone LIKE '.$db->Quote('center_col%'));
//        $query->order('rgt DESC');
//        $db->setQuery($query);
//        $retult = $db->loadObject();
//        return $retult->rgt + 1;
//    } 
//    
//    public function getCategory($contentID){
//        $db =& $this->_sourceDB;
//        $query = $db->getQuery(true);
//        $query->select(
//            '*'
//        );
//        $query->from($db->nameQuote(self::$_sourceCategoryTableName));
//        $query->where('item_id = '.(int)$contentID);
//        $db->setQuery($query);
//        $result = $db->loadObject();
//        $catid = $result->cid;
//        if($catid == 0){
//            $catid = 2;
//        }
//        return $catid;
//    } 
//    
//    protected function setAsset($contentId, $catId, $name){
//        $nextRGT = $this->getNextRGT();
//        $db =& $this->_destDB;
//        $query = $db->getQuery(true);
//        $query->insert($db->nameQuote(self::$_destAssetsTableName));
//        $query->set('parent_id = '.(int)$catId);
//        $query->set('lft = '.(int)$nextRGT);
//        $query->set('rgt = '.(int)$nextRGT+1);
//        $query->set('level = '.(int)3);
//        $query->set('name = '.$db->Quote('com_content.article.'.(int)$contentId));
//        $query->set('title = '.$db->Quote($name));
//        $query->set('rules = '.$db->Quote('{"core.delete":[],"core.edit":[],"core.edit.state":[]}'));
//        $db->setQuery($query);
//        $result = $db->query();
//        $lastId = $db->insertid();
//        return $lastId;
//    }
    
    protected function nullToSpace($str){
        if($str == null){
            return '';
        }else if($str == '-'){
            return '';
        }
        return $str;
    }
    
    
    protected function cleanField($field){
        $strippedWhites = trim($field);
        return $this->nullToSpace($strippedWhites);
    }
    
    protected function serializeUser($post, $avdelning = 'Trollhättan'){
        $namn = $this->cleanField($post['Fornamn']).' '.$this->cleanField($post['Efternamn']);
        $fodelsedatum = $this->cleanField($post['Fodelsedatum']);
        $teleArb = $this->cleanField($post['Telefon arb']);
        $adrArb = $this->cleanField($post['Adress arb']);
        $postArb = $this->cleanField($post['Postadress arb']);
        $adrHem = $this->cleanField($post['Adress hem']);
        $postHem = $this->cleanField($post['Postadress hem']);
        $teleHem = $this->cleanField($post['Telefon hem']);
        $titel = $this->cleanField($post['Titel']);
        $arbGivare = $this->cleanField($post['Arbetsgivare']);
        $reklam = $this->cleanField($post['Reklam information']);
        $userObject = new stdClass();
        $userObject->name = $namn;
        $userObject->username = '';
        $userObject->fields = array(
            'foedelsedatum' => $fodelsedatum,
            'telefon-arbete' => $teleArb,
            'adress-arbete' => $adrArb,
            'postadress-arbete' => $postArb,
            'adress-hem' => $adrHem,
            'postadress-hem' => $postHem,
            'telefon-hem' => $teleHem,
            'titel' => $titel,
            'arbetsgivare' => $arbGivare,
            'avdelning' => array(
                $avdelning
                ),
            'reklam' => $reklam
        );
        $serialized = serialize($userObject);
        return $serialized;

    }
    
//    protected function createUser($email, $data){
//        RSMembershipHelper::getConfig('create_user_instantly');
//        RSMembershipHelper::createUser($email, $data);
//    }
//    
//    protected function post($post){
//        $url = "http://localhost:8080/websites/flygtekniska/index.php";
//        $post_data = array(
//            'name' => $post['Fornamn'].' '.$post['Efternamn'],
//            'email' => $post['E-mail'],
//            'rsm_fields[foedelsedatum]' => $post['Fodelsedatum'],
//            'rsm_fields[telefon-arbete]' => $post['Telefon arb'],
//            'rsm_fields[adress-arbete]' => $post['Adress arb'],
//            'rsm_fields[postadress-arbete]' => $post['Postadress arb'],
//            'rsm_fields[adress-hem]' => $post['Adress hem'],
//            'rsm_fields[postadress-hem]' => $post['Postadress hem'],
//            'rsm_fields[telefon-hem]' => $post['Telefon hem'],
//            'rsm_fields[titel]' => $post['Titel'],
//            'rsm_fields[arbetsgivare]' => $post['Arbetsgivare'],
//            'rsm_fields[avdelning][]' => $post['Avdelning'],
//            'rsm_fields[reklam]' => $post['Reklam information'],
//            'payment' => 'rsmembershipwire1',
//            'view' => 'subscribe',
//            'task' => 'validatesubscribe',
//            'option' => 'com_rsmembership',
//            'cid' => '1',
//            JUtility::getToken() => '1'
//        );
//        var_dump($post_data);exit;
//        $post_string = http_build_query($post_data);
//        $this->curlit($url, $post_string);
//    }
//    
//    protected function curlit($url, $post_string){
//        $curl_connection = curl_init($url);
//        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
//        curl_setopt($curl_connection, CURLOPT_USERAGENT,
//        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
//        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, false);
//        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
//        $result = curl_exec($curl_connection);
//        curl_close($curl_connection);
//        return $result;
//    }
//    
//    protected function sockit($url, $post_string){
//        $connection = fsockopen('localhost', 8080);
//        //sending the data
//        fputs($connection, "POST  /websites/flygtekniska/index.php  HTTP/1.1\r\n");
//        fputs($connection, "Host:  localhost \r\n");
//        fputs($connection,
//            "Content-Type: application/x-www-form-urlencoded\r\n");
//        fputs($connection, "Content-Length: $data_length\r\n");
//        fputs($connection, "Connection: close\r\n\r\n");
//        fputs($connection, $post_string);
//        //closing the connection
//        fclose($connection);
//        return $connection;
//    }

    public function migrateMembers(){
        $isSuccessful = true;
        $members = $this->getMembers();
        foreach ($members as $member) {
            $serializedData = $this->serializeUser($member);
            $isSuccessful *= $this->setMember($member['E-mail'], $serializedData, $member['Datum betalt']);
        }
        return (bool)$isSuccessful;
    }
        

    public function __set($name, $value) {
        $this->$name = $value;
    }

}
