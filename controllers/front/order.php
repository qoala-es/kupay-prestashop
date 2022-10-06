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

require_once dirname(__FILE__) . '../../../services/kupay_order_service.php';
require_once dirname(__FILE__) . '../../../services/kupay_authentication_service.php';
require_once dirname(__FILE__) . '../../../services/kupay_log_service.php';

class KupayOrderModuleFrontController extends ModuleFrontController
{
    public function run()
    {
        header('Content-Type: ' . "application/json");

        $payload = json_decode(Tools::file_get_contents('php://input'), true);
        KupayAuthenticationService::authenticate($payload);

        parent::init();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->processPostRequest();
                break;
            default:
                $this->processNotSupportedRequest();
        }
    }

    protected function processPostRequest()
    {
        try {

            $payload = json_decode(Tools::file_get_contents('php://input'), true);

            $order = KupayOrderService::create($payload);

            $this->ajaxRender(json_encode($order));
        } catch (Exception $e) {

            http_response_code(500);

            KupayLogService::logNewRelic("ERROR", "Post Request Error | " . $e->getMessage(), "order", $e->getTraceAsString());

            $this->ajaxRender(json_encode([
                'message' => $e->getMessage()
            ]));
        }
    }

    protected function processNotSupportedRequest()
    {
        http_response_code(405);

        $this->ajaxRender(json_encode([
            'message' => "Method Not Allowed."
        ]));
    }
}
