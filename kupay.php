<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/services/kupay_routes_service.php';

class Kupay extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'kupay';
        $this->tab = 'front_office_features';
        $this->version = '1.0.6';
        $this->author = 'Kupay';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        Configuration::set('KUPAYMODULE_IFRAME_URL', 'https://checkout.kupay.co/#/order-received');

        if (Configuration::get('KUPAYMODULE_URLKUPAYCHECKOUT')) {
            Configuration::set('KUPAYMODULE_IFRAME_URL', Configuration::get('KUPAYMODULE_URLKUPAYCHECKOUT') . '/#/order-received');
        }
        if (Configuration::get('KUPAYMODULE_TESTMODE')) {
            Configuration::set('KUPAYMODULE_IFRAME_URL', 'http://localhost:3001/#/order-received');
        }

        parent::__construct();

        $this->displayName = $this->l('Kupay');
        $this->description = $this->l('Kupay 1-Click Checkout');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return (parent::install()
            && $this->registerHook('actionFrontControllerSetMedia')
            && Configuration::updateValue('KUPAYMODULE_NAME', 'Kupay 1.0.4')
            && $this->registerHook('moduleRoutes')
        );
    }

    public function uninstall()
    {
        return (parent::uninstall()
            && Configuration::deleteByName('KUPAYMODULE_NAME')
        );
    }

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm()
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('APP ID'),
                        'name' => 'KUPAYMODULE_APPID',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('API Key'),
                        'name' => 'KUPAYMODULE_APIKEY',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('URL Kupay Checkout'),
                        'name' => 'KUPAYMODULE_URLKUPAYCHECKOUT',
                        'size' => 20,
                        'placeholder' => 'https://checkout.kupay.co',
                        'desc' => 'Please, note that you should not change the field below if it was not advised by a Kupay employee.',

                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Test Mode'),
                        'name' => 'KUPAYMODULE_TESTMODE',
                        'size' => 20,
                        'is_bool' => true,
                        'required' => true,
                        'values' => array(
                            array(
                                'id' => 'ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable checkout button on Product Page'),
                        'name' => 'KUPAYMODULE_PDP',
                        'size' => 20,
                        'required' => true,
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'ON',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'OFF',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
                    ],
                    // [
                    //     'type' => 'switch',
                    //     'label' => $this->l('Enable checkout button on Cart Page'),
                    //     'name' => 'KUPAYMODULE_CART',
                    //     'size' => 20,
                    //     'required' => true,
                    //     'is_bool' => true,
                    //     'values' => array(
                    //         array(
                    //             'id' => 'ON',
                    //             'value' => true,
                    //             'label' => $this->l('Enabled')
                    //         ),
                    //         array(
                    //             'id' => 'OFF',
                    //             'value' => false,
                    //             'label' => $this->l('Disabled')
                    //         )
                    //     )
                    // ],
                    // [
                    //     'type' => 'switch',
                    //     'label' => $this->l('Enable checkout button on Checkout Page'),
                    //     'name' => 'KUPAYMODULE_CHECKOUT',
                    //     'size' => 20,
                    //     'required' => true,
                    //     'is_bool' => true,
                    //     'values' => array(
                    //         array(
                    //             'id' => 'ON',
                    //             'value' => true,
                    //             'label' => $this->l('Enabled')
                    //         ),
                    //         array(
                    //             'id' => 'OFF',
                    //             'value' => false,
                    //             'label' => $this->l('Disabled')
                    //         )
                    //     )
                    // ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Enable Kupay Exclusively For These Products (Separate ids by comma)'),
                        'name' => 'KUPAYMODULE_PRODUCT_IDS',
                        'size' => 20,
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Cart page styles (CSS)'),
                        'name' => 'KUPAYMODULE_STYLES_CART',
                        'size' => 40,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Checkout page styles (CSS)'),
                        'name' => 'KUPAYMODULE_STYLES_CHECKOUT',
                        'size' => 40,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Product page styles (CSS)'),
                        'name' => 'KUPAYMODULE_STYLES_PRODUCT',
                        'size' => 40,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value = [
            'KUPAYMODULE_APPID' => Tools::getValue('KUPAYMODULE_APPID', Configuration::get('KUPAYMODULE_APPID')),
            'KUPAYMODULE_APIKEY' => Tools::getValue('KUPAYMODULE_APIKEY', Configuration::get('KUPAYMODULE_APIKEY')),
            'KUPAYMODULE_URLKUPAYCHECKOUT' => Tools::getValue('KUPAYMODULE_URLKUPAYCHECKOUT', Configuration::get('KUPAYMODULE_URLKUPAYCHECKOUT')),
            'KUPAYMODULE_TESTMODE' => Tools::getValue('KUPAYMODULE_TESTMODE', Configuration::get('KUPAYMODULE_TESTMODE')),
            'KUPAYMODULE_PDP' => Tools::getValue('KUPAYMODULE_PDP', Configuration::get('KUPAYMODULE_PDP')),
            'KUPAYMODULE_CART' => Tools::getValue('KUPAYMODULE_CART', Configuration::get('KUPAYMODULE_CART')),
            'KUPAYMODULE_CHECKOUT' => Tools::getValue('KUPAYMODULE_CHECKOUT', Configuration::get('KUPAYMODULE_CHECKOUT')),
            'KUPAYMODULE_STYLES_CART' => Tools::getValue('KUPAYMODULE_STYLES_CART', Configuration::get('KUPAYMODULE_STYLES_CART')),
            'KUPAYMODULE_STYLES_CHECKOUT' => Tools::getValue('KUPAYMODULE_STYLES_CHECKOUT', Configuration::get('KUPAYMODULE_STYLES_CHECKOUT')),
            'KUPAYMODULE_STYLES_PRODUCT' => Tools::getValue('KUPAYMODULE_STYLES_PRODUCT', Configuration::get('KUPAYMODULE_STYLES_PRODUCT')),
            'KUPAYMODULE_PRODUCT_IDS' => Tools::getValue('KUPAYMODULE_PRODUCT_IDS', Configuration::get('KUPAYMODULE_PRODUCT_IDS')),
        ];

        return $helper->generateForm([$form]);
    }

    /**
     * This method handles the module's configuration page
     * @return string The page's HTML content
     */
    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $appId = (string) Tools::getValue('KUPAYMODULE_APPID');
            $apiKey = (string) Tools::getValue('KUPAYMODULE_APIKEY');
            $urlCheckout = (string) Tools::getValue('KUPAYMODULE_URLKUPAYCHECKOUT');
            $testMode = (string) Tools::getValue('KUPAYMODULE_TESTMODE');
            $isPdp = (string) Tools::getValue('KUPAYMODULE_PDP');
            $isCart = (string) Tools::getValue('KUPAYMODULE_CART');
            $isCheckout = (string) Tools::getValue('KUPAYMODULE_CHECKOUT');
            $cartStyles = (string) Tools::getValue('KUPAYMODULE_STYLES_CART');
            $checkoutStyles = (string) Tools::getValue('KUPAYMODULE_STYLES_CHECKOUT');
            $productStyles = (string) Tools::getValue('KUPAYMODULE_STYLES_PRODUCT');
            $productIds = (string) Tools::getValue('KUPAYMODULE_PRODUCT_IDS');

            // check that the value is valid
            if (empty($appId) || !Validate::isGenericName($appId)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('KUPAYMODULE_APPID', $appId);
                Configuration::updateValue('KUPAYMODULE_APIKEY', $apiKey);
                Configuration::updateValue('KUPAYMODULE_URLKUPAYCHECKOUT', $urlCheckout);
                Configuration::updateValue('KUPAYMODULE_TESTMODE', $testMode);
                Configuration::updateValue('KUPAYMODULE_PDP', $isPdp);
                Configuration::updateValue('KUPAYMODULE_CART', $isCart);
                Configuration::updateValue('KUPAYMODULE_CHECKOUT', $isCheckout);
                Configuration::updateValue('KUPAYMODULE_STYLES_CART', $cartStyles);
                Configuration::updateValue('KUPAYMODULE_STYLES_CHECKOUT', $checkoutStyles);
                Configuration::updateValue('KUPAYMODULE_STYLES_PRODUCT', $productStyles);
                Configuration::updateValue('KUPAYMODULE_PRODUCT_IDS', $productIds);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    private function isEnableForProduct($product_id): bool
    {


        if (!empty(Configuration::get('KUPAYMODULE_PRODUCT_IDS'))) {

            $selected_products_ids = explode(",", Configuration::get('KUPAYMODULE_PRODUCT_IDS'));

            return in_array((string)$product_id, $selected_products_ids);
        }


        return true;
    }

    private function isEnableForProductsInCart(array $product_ids): bool
    {

        if (!empty(Configuration::get('KUPAYMODULE_PRODUCT_IDS'))) {

            $selected_products_ids = explode(",", Configuration::get('KUPAYMODULE_PRODUCT_IDS'));

            foreach ($product_ids as $product_id) {

                if (!in_array($product_id, $selected_products_ids)) {
                    return false;
                }
            }
        }


        return true;
    }

    private function getCartProductsIds(Cart $cart): array
    {

        $products_ids = [];

        if (!empty($cart->getProducts())) {

            foreach ($cart->getProducts() as $product) {
                array_push($products_ids, $product['id_product']);
            }
        }

        return $products_ids;
    }

    public function hookActionFrontControllerSetMedia($params)
    {

        $context = Context::getContext();

        // $context->controller = new FrontController();

        // var_dump($context->controller->getProduct());


        $assetsUrl = $this->_path . 'views/';

        $this->context->controller->addCSS($assetsUrl . 'css/kupay.css');
        $this->context->controller->addJS($assetsUrl . 'js/kupay.js');
        $this->context->controller->addJS($assetsUrl . 'js/kupay-quickview.js');

        if ($this->context->controller->getPageName() === 'product') {

            Media::addJsDef(array('kupay' => array(
                'appId' => Configuration::get('KUPAYMODULE_APPID'),
                'product' => $this->context->controller->getProduct(),
                'iframeUrl' => Configuration::get('KUPAYMODULE_IFRAME_URL'),
                'moduleId' => $this->id,
                'cartStyles' => Configuration::get('KUPAYMODULE_STYLES_CART'),
                'checkoutStyles' => Configuration::get('KUPAYMODULE_STYLES_CHECKOUT'),
                'productStyles' => Configuration::get('KUPAYMODULE_STYLES_PRODUCT'),
            )));
        } else {

            Media::addJsDef(array('kupay' => array(
                'appId' => Configuration::get('KUPAYMODULE_APPID'),
                'cartId' => $this->context->cart->id,
                'iframeUrl' => Configuration::get('KUPAYMODULE_IFRAME_URL'),
                'moduleId' => $this->id,
                'cartStyles' => Configuration::get('KUPAYMODULE_STYLES_CART'),
                'checkoutStyles' => Configuration::get('KUPAYMODULE_STYLES_CHECKOUT'),
                'productStyles' => Configuration::get('KUPAYMODULE_STYLES_PRODUCT'),
            )));
        }

        if (Configuration::get(('KUPAYMODULE_PDP'))) {

            if ($this->context->controller->getPageName() === 'product' && $this->isEnableForProduct($this->context->controller->getProduct()->id)) {
                $this->context->controller->addJS($assetsUrl . 'js/kupay-pdp.js');
                // $this->context->controller->registerJavascript()


            }
        }

        if (Configuration::get(('KUPAYMODULE_CART'))) {

            if ($this->context->controller->getPageName() === 'cart' && $this->isEnableForProductsInCart($this->getCartProductsIds($this->context->cart))) {
                $this->context->controller->addJS($assetsUrl . 'js/kupay-cart.js');
            }
        }

        if (Configuration::get(('KUPAYMODULE_CHECKOUT'))) {

            if ($this->context->controller->getPageName() === 'checkout' && $this->isEnableForProductsInCart($this->getCartProductsIds($this->context->cart))) {
                $this->context->controller->addJS($assetsUrl . 'js/kupay-checkout.js');
            }
        }
    }

    public function hookModuleRoutes()
    {
        return KupayRoutes::getRoutes();
    }
}
