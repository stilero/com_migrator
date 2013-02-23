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

class MigrateStatus{
    
    const PENDING = 1;
    const PROCESSING = 2;
    const SHIPPED = 3;
    const COMPLETE = 5;
    const CANCELED = 7;
    const DENIED = 8;
    const CANCELED_REVERSAL = 9;
    const FAILED = 10;
    const REFUNDED = 11;
    const REVERSED = 12;
    const CHARGEBACK = 13;
    const EXPIRED = 14;
    const PROCESSED = 15;
    const VOIDED = 16;
    const VM_PENDING = 'P';
    const VM_CONFIRMED_BY_SHOPPER = 'U';
    const VM_CONFIRMED = 'C';
    const VM_CANCELLED = 'X';
    const VM_REFUNDED = 'R';
    const VM_SHIPPED = 'S';
    public static $mapping = array(
        self::VM_PENDING => array(
            self::PENDING
        ),
        self::VM_CONFIRMED_BY_SHOPPER => array(
            self::PROCESSING
        ),
        self::VM_CONFIRMED => array(
            self::PROCESSED
        ),
        self::VM_CANCELLED => array(
            self::CANCELED,
            self::CANCELED_REVERSAL,
            self::DENIED,
            self::FAILED,
            self::EXPIRED,
            self::VOIDED
        ),
        self::VM_REFUNDED => array(
            self::REFUNDED,
            self::REVERSED,
            self::CHARGEBACK
        ),
        self::VM_SHIPPED => array(
            self::SHIPPED,
            self::COMPLETE
        )
    );

    public static function convertToVmOrderStatus($ocStatus){
        foreach (self::$mapping as $vmStatus => $ocStatuses) {
            if(in_array($ocStatus, $ocStatuses)){
                return $vmStatus;
            }
        }
    }
}
