<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-17 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$assets_uri = JURI::base().'components/com_shopmigrator/assets';
$bootstrap_uri = $assets_uri.'/bootstrap';
        $document =& JFactory::getDocument();
        $jsTranslationStrings = 'var PLG_SYSTEM_AUTOFBOOK_JS_SUCCESS = "'.JText::_('PLG_SYSTEM_AUTOFBOOK_JS_SUCCESS').'";';
        $jsTranslationStrings .= 'var PLG_SYSTEM_AUTOFBOOK_JS_FAILURE = "'.JText::_('PLG_SYSTEM_AUTOFBOOK_JS_FAILURE').'";';
        $document->addScriptDeclaration($jsTranslationStrings);     
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Migrate</title>
        <link href="<?php print $bootstrap_uri.'/css/bootstrap.min.css'  ?>" rel="stylesheet">
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="<?php print $bootstrap_uri.'/js/bootstrap.min.js' ?>"></script>
        <script type="text/javascript">
            var tasks = [<?php echo '\''.implode('\', \'', $this->tasks).'\'' ;?>];
            var totalTasks = <?php echo count($this->tasks); ?> * 2;
            var remainingTasks = <?php echo count($this->tasks); ?> * 2;
            var currentTask = 0;
            var isErrorFound = false;
            jQuery.noConflict();
            jQuery(function($){
                
                var doMigration = function(){
                    //alert('cur:' + currentTask + ' total:'+tasks.length);
                    if((currentTask < tasks.length) && isErrorFound === false){
                        setProgressBar();
                        migrate(tasks[currentTask++]);
                    }
                } 
                
                var setDone = function(){
                    $('div[class^=progress]').attr('class','progress progress-success progress-striped');
                    $('<div class="alert alert-success">Shop Successfully Migrated!</div>').insertAfter('div[class^=progress]');
                    $('<div class="alert alert-success">Shop Successfully Migrated!</div>').appendTo('.container-fluid');
                    //$('<div class="row"><div class="span9"><div class="well"><span class="label label-'+labelClass+'">'+labelText+'</span> '+migrCmd+' '+labelText+': '+errorText+'</div></div></div>').appendTo('.container');

                }
                
                var setProgressBar= function(){
                    remainingTasks -= 1;
                    var totalProgress = 100 - 100*(remainingTasks/totalTasks);
                    $('#totalbar').css('width', totalProgress + '%');
                }
                
                var addReport = function(data, migrCmd){
                    var labelClass = 'success';
                    var labelText = 'Success';
                    var errorText = '';
                    if(data.code != 0){
                        isErrorFound = true;
                        $('div[class^=progress]').attr('class','progress progress-danger progress-striped');
                        labelClass = 'important';
                        labelText = 'error';
                        errorText = data.message;
                    }
                    $('<div class="row"><div class="span9"><div class="well"><span class="label label-'+labelClass+'">'+labelText+'</span> '+migrCmd+' '+labelText+': '+errorText+'</div></div></div>').appendTo('.container-fluid');
                };
                
                var handleResponse = function(data, migrCmd){
                    setProgressBar();
                    addReport(data, migrCmd);
                };
                
               var migrate = function(migrCmd){
                   var requestData = {
                       option: '<?php echo JRequest::getVar('option'); ?>',
                       view: '<?php echo $this->class; ?>',
                       format: 'raw',
                       migrateCmd: migrCmd                       
                   };
                   $.getJSON('index.php?<?php print JUtility::getToken() ?>=1', requestData, function(data){
                       handleResponse(data, migrCmd);
                   }).error(function() { 
                       var errorData = new Object();
                       errorData.code = '1';
                       errorData.message = 'Server error';
                       handleResponse(errorData, migrCmd);
                   }).complete(function(){
                         if((currentTask < tasks.length) && isErrorFound === false){
                            doMigration();
                        }else if(isErrorFound === false){
                            setDone();
                        }
                   });
               };  
               doMigration();
//               $.each(tasks, function(index, value){
//                   setProgressBar();
//                   migrate(value);
//               });
            });
        </script>
    </head>
    <body>
        <div class="container-fluid">
                <div class="page-header">
                    <h1>Migrating<small> Don't close this window.</small></h1>
                </div>
            <div class="row">
                <div class="span9">
                    <div class="progress progress-striped active">
                        <div id="totalbar" class="bar" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
</html>