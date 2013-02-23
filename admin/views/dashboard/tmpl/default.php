<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-20 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$params = & JComponentHelper::getParams('com_shopmigrator');
$src = array(
    'db_host' => $params->get('db_host'),
    'db_name' => $params->get('db_name'),
    'db_user' => $params->get('db_user'),
    'db_pass' => $params->get('db_pass'),
);
?>
<?php if ($params->get('db_host') != null && $params->get('db_host') != '' ) : ?>
    <div class="cpanel">
        <div class="icon-wrapper">
            <div class="icon">
                <a class="modal" href="index.php?option=com_shopmigrator&view=migration&format=raw&systemid=1" rel="{handler: 'iframe', size: {x: 800, y: 550}, onClose: function() {}}">
                    <img src="<?php echo JURI::root();?>/administrator/components/com_shopmigrator/assets/images/icon-48-migration.png" alt=""  />
                    <span>Start Migration</span>
                </a>
            </div>
        </div>
        <div class="icon-wrapper">
            <div class="icon">
                <a class="modal" href="index.php?option=com_shopmigrator&view=migration&format=raw&systemid=2" rel="{handler: 'iframe', size: {x: 800, y: 550}, onClose: function() {}}">
                    <img src="<?php echo JURI::root();?>/administrator/components/com_shopmigrator/assets/images/icon-48-migration.png" alt=""  />
                    <span>Migrate ModX</span>
                </a>
            </div>
        </div>
        <div class="icon-wrapper">
            <div class="icon">
                <a class="modal" href="index.php?option=com_shopmigrator&view=migration&format=raw&systemid=3" rel="{handler: 'iframe', size: {x: 800, y: 550}, onClose: function() {}}">
                    <img src="<?php echo JURI::root();?>/administrator/components/com_shopmigrator/assets/images/icon-48-migration.png" alt=""  />
                    <span>Migrate Flygtekn</span>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <h2>No configurations set</h2>
<?php endif; ?>
