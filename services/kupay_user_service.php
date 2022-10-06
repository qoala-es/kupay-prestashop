<?php
/**
 * NOTICE OF LICENSE
 *
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Contributors: kupayco
 * kupay.co
 *
 * Tags:
 *      checkout,
 *      1-click-checkout,
 *      one-click-checkout,
 *      prestashop,
 *      kupay,
 *      e-commerce,
 *      store,
 *      sales,
 *      sell,
 *      shop,
 *      cart,
 *      checkout,
 *      payments
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Kupayco
 *  @copyright 2021 Kupay.co
 *  @license   GPLv2 or later
 * @license-file license.txt
 */

require_once dirname(__FILE__) . '../../services/kupay_address_service.php';
require_once dirname(__FILE__) . '../../services/kupay_log_service.php';

class KupayUserService
{


    public static function create($shopper){
        
        $customer = new Customer();

        $existing_customers = Customer::getCustomersByEmail($shopper['email']);

        if(!empty($existing_customers)){
            return self::update($shopper);
        }

        $name = explode(" ", $shopper['name']);
        $customer->firstname = $name[0];
        $customer->lastname = $name[1];
        $customer->email = $shopper['email'];
        $customer->active = 1;
        $customer->is_guest = 0;
        $customer->passwd = uniqid();

        $customer->add();
    
        KupayAddressService::create($shopper, $customer);

        KupayLogService::logNewRelic("INFO", "User (ID: $customer->id) Create", "user");

        return $customer;

    }

    public static function update($shopper){

        $customer = self::getExistingCustomer($shopper);

        $name = explode(" ", $shopper['name']);

        $customer->firstname = $name[0];
        $customer->lastname = $name[1];
        $customer->email = $shopper['email'];
        $customer->active = 1;
        $customer->is_guest = 0;
        $customer->passwd = uniqid();

        $customer->update();
        
        KupayAddressService::update($shopper, $customer);

        KupayLogService::logNewRelic("INFO", "User (ID: $customer->id) Update", "user");

        return $customer;

    }

    private static function getExistingCustomer($shopper): Customer{

        $existing_customers = Customer::getCustomersByEmail($shopper['email']);

        $customer = new Customer($existing_customers[0]['id_customer']);
        return $customer;

    }



}