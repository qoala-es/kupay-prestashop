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

require_once dirname(__FILE__) . '../../services/kupay_log_service.php';

class KupayAuthenticationService
{

    public static function authenticate()
    {
        if (!isset(getallheaders()["Authorization"]) || getallheaders()["Authorization"] !== Configuration::get('KUPAYMODULE_APIKEY')) {

            KupayLogService::logNewRelic("ERROR", "Authentication error. API-Key: " . getallheaders()["Authorization"], null, "authentication");

            http_response_code(403);
            exit;
        }
    }
}
