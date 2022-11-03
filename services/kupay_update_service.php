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

class KupayUpdateService
{
    // Execute git commands to pull updates from public repo
    public static function executeCmdInBackground($cmd)
    {
        try {

            if (substr(php_uname(), 0, 7) == "Windows") {
                $os = 'Windows';
                exec($cmd . " 2>&1", $output, $retval);
            } else {
                $os = 'Linux/Unix';
                exec($cmd, $output, $retval);
            }
            
            $outputString = "";

            if (count($output) > 1) {
                foreach ($output as $value) {
                    $outputString .= $value . " - ";
                }
            } else {
                $outputString = $output[0];
            }

            KupayLogService::logNewRelic("INFO", "Execution of command in bg (on $os): " . $cmd . " | Output: " . $outputString, "update");

        } catch (Exception $e) {

            KupayLogService::logNewRelic("ERROR", "Execution of command in bg (on $os): " . $cmd . " | " . $e->getMessage(), "update", $e->getTraceAsString());

            return "Error";
        }

        // Returns the first output to validate if it shows an info or success alert in the configuration
        return $output[0];
    }
}
