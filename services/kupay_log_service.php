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

class KupayLogService
{

    public static function logNewRelic($type, $msg, $service, $trace = '', $env = 'PROD') {

        $apiKey = 'eu01xx6b5da31d9438593b25a43a0097FFFFNRAL';

        // Get Kupay module version
        $module = Module::getInstanceByName('kupay');
        $version = $module->version;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://log-api.eu.newrelic.com/log/v1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "message": "'.$msg.'",
            "logtype": "'.$type.'",
            "platform": "prestashop",
            "service": "'.$service.'",
            "hostname": "'.$_SERVER['SERVER_NAME'].'",
            "trace": "'.$trace.'",
            "version": "'.$version.'",
            "environment": "'.$env.'"
        }',
        CURLOPT_HTTPHEADER => array(
            "Api-Key: $apiKey",
            "Content-Type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

    }

}