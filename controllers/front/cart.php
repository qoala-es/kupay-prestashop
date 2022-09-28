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


if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '../../../services/kupay_cart_service.php';
require_once dirname(__FILE__) . '../../../services/kupay_user_service.php';

class KupayCartModuleFrontController extends ModuleFrontController
{
    /**
     * @throws PrestaShopException
     */
    public function run()
    {
        header('Content-Type: ' . "application/json");
        parent::init();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->processGetRequest();
                break;
            case 'POST':
                $this->processPostRequest();
                break;
            case 'PUT':
                $this->processPutRequest();
                break;
            default:
                $this->processNotSupportedRequest();
        }
    }

    protected function processGetRequest() {

        try {
            $payload = json_decode(Tools::file_get_contents('php://input'), true);
            $url = $_SERVER['REQUEST_URI'];
            $url = parse_url($url);
            parse_str($url['query'], $params);
            $code = $params['code'];

            $cart = KupayCartService::retrieve($code, $payload);

            $this->ajaxRender(json_encode($cart));

            
        } catch (Exception $e) {
            
            http_response_code(500);

            $this->ajaxRender(json_encode([
                'message' => $e->getMessage(),
                'trace' => json_encode($e->getTrace())
            ]));
            
        }

    }

    protected function processPostRequest() {

        try {

            $payload = json_decode(Tools::file_get_contents('php://input'), true);

            $customer = KupayUserService::create($payload['shopper']);

            $cart = KupayCartService::create($customer, $payload);

            $this->ajaxRender(json_encode($cart));
            
        } catch (Exception $e) {
            
            http_response_code(500);

            $this->ajaxRender(json_encode([
                'message' => $e->getMessage(),
                'trace' => json_encode($e->getTrace())
            ]));
            
        }
    }

    protected function processPutRequest() {

        try {

            $payload = json_decode(Tools::file_get_contents('php://input'), true);
            
            $customer = KupayUserService::update($payload['shopper']);
            $cart = KupayCartService::update($customer, $payload);

            $this->ajaxRender(json_encode($cart));
            
        } catch (Exception $e) {
            
            http_response_code(500);

            $this->ajaxRender(json_encode([
                'message' => $e->getMessage(),
                'trace' => json_encode($e->getTrace())
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
