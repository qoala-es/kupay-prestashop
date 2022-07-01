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

class KupayAddressService
{


    public static function create(array $shopper, Customer $customer){

        $shippingAddress = new Address();
        $shippingAddress->id_customer = $customer->id;
        $shippingAddress->alias = 'Shipping Address';
        $shippingAddress->firstname = $customer->firstname;
        $shippingAddress->lastname = $customer->lastname;
        $shippingAddress->address1 = $shopper['shippingAddress']['address'];
        $shippingAddress->address2 = $shopper['shippingAddress']['secondAddress'];
        $shippingAddress->city = $shopper['shippingAddress']['city'];
        $shippingAddress->postcode = $shopper['shippingAddress']['zipCode'];
        $shippingAddress->phone = $shopper['phoneNumber'];
        $shippingAddress->dni = $shopper['document'];
        $shippingAddress->id_country = Country ::getByIso($shopper['shippingAddress']['countryCode']); // to be sent from the API

        $shippingAddress->add();

    }

    public static function update(array $shopper, Customer $customer){

        $addresses = $customer->getAddresses($customer->id_lang);
        
        foreach($addresses as $address_item){

            $address = new Address($address_item['id_address']);

            $address->id_customer = $customer->id;
            $address->alias = 'Shipping Address';
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->address1 = $shopper['shippingAddress']['address'];
            $address->address2 = $shopper['shippingAddress']['secondAddress'];
            $address->city = $shopper['shippingAddress']['city'];
            $address->postcode = $shopper['shippingAddress']['zipCode'];
            $address->phone = $shopper['phoneNumber'];
            $address->dni = $shopper['document'];
            $address->id_country = Country ::getByIso($shopper['shippingAddress']['countryCode']); // to be sent from the API

            $address->update();
            
        }

        // self::removeAddresses($customer);
        // self::create($shopper, $customer);

    }

    public static function removeAddresses(Customer $customer){

        $addresses = $customer->getAddresses($customer->id_lang);
        
        foreach($addresses as $address_item){

            $address = new Address($address_item['id_address']);
            $address->delete();
        }
        
    }


}