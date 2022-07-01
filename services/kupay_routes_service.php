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

class KupayRoutes
{
    final public static function getRoutes(): array
    {
        return [

            'module-kupay-cart' => [
                'rule' => 'kupay/cart',
                'keywords' => [],
                'controller' => 'cart',
                'params' => [
                    'fc' => 'module',
                    'module' => 'kupay'
                ]
            ],
            'module-kupay-order' => [
                'rule' => 'kupay/order',
                'keywords' => [],
                'controller' => 'order',
                'params' => [
                    'fc' => 'module',
                    'module' => 'kupay'
                ]
            ]
        ];
    }
}
