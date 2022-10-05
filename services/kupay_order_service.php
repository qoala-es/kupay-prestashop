<?php

/**
 * NOTICE OF LICENSE
 *
 * License = GPLv2 or later
 * License URI = http =//www.gnu.org/licenses/gpl-2.0.html
 * Contributors = kupayco
 * kupay.co
 *
 * Tags =
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

require_once dirname(__FILE__) . './../services/kupay_cart_service.php';

class KupayOrderService
{

    public static function create($payload)
    {

        $paymentMethods = PaymentModuleCore::getInstalledPaymentModules();
        $paymentModule = Module::getInstanceByName($paymentMethods[0]['name']);
        $paymentName = $paymentModule->name;

        $cart = new Cart($payload['cartId']);

        $order = new Order();
        $order->id_customer = $cart->id_customer;
        $order->id_address_delivery = $cart->id_address_delivery;
        $order->id_address_invoice = $cart->id_address_delivery;
        $order->id_cart = $cart->id;
        $order->id_currency = (int)  $cart->id_currency;
        $order->id_carrier = $cart->id_carrier;
        $order->id_lang = (int) $cart->id_lang;
        $order->payment = "Qoala";
        $order->module = $paymentName;
        $order->total_paid = (float) $cart->getOrderTotal();
        $order->total_paid_real = (float) $cart->getOrderTotal();
        $order->total_products = (float) number_format(KupayCartService::calculateProductsTotal($cart), 2);
        $order->total_products_wt = (float) number_format(KupayCartService::calculateProductsTotal($cart), 2);
        $order->total_discounts = 0;
        $order->total_discounts_tax_incl = 0;
        $order->total_discounts_tax_excl = 0;
        $order->total_paid_tax_incl = (float) number_format($cart->getOrderTotal(), 2);
        $order->total_paid_tax_excl = (float) number_format($cart->getOrderTotal(false), 2);
        $order->total_shipping = (float) number_format($cart->getTotalShippingCost(), 2);
        $order->total_discounts = (float) number_format($cart->getDiscountSubtotalWithoutGifts(true), 2);
        $order->total_shipping_tax_incl = (float) number_format($cart->getTotalShippingCost(), 2);
        $order->total_shipping_tax_excl = (float) number_format($cart->getTotalShippingCost(), 2);
        $order->id_shop = $cart->id_shop;
        $order->conversion_rate = 1;
        $order->secure_key = $cart->secure_key;
        $order->note = "Placed via Qoala 1-Click Checkout";
        $order->reference = Order::generateReference();
        $order->current_state = Configuration::get('PS_OS_OUTOFSTOCK_UNPAID');
        $order->date_add = date('Y-m-d H:i:s');
        $order->date_upd = date('Y-m-d H:i:s');
        $order->current_state = 3;

        $order->add();

        $logger = new KupayLogService();
        $logger::logNewRelic("INFO", "Order (ID: $order->id) create", "order");

        self::addProducts($order, $cart);

        self::createOrderPaymentTransaction($order);

        return self::buildOrderData($order);
    }

    public static function addCoupons(Order $order, Cart $cart)
    {
        // foreach($cart->getCartRules() as $cartRules){
        //     $order->addCartRule()
        // }
    }

    public static function addProducts(Order $order, Cart $cart)
    {

        foreach ($cart->getPackageList() as $packageInnerList) {

            foreach ($packageInnerList as $package) {

                foreach ($package['product_list'] as $product) {

                    $orderDetail = new OrderDetail();

                    $orderDetail->product_id = $product['id_product'];
                    // If there are attributes, we set them in the product name
                    $orderDetail->product_name = $product['name'] .
                    ((isset($product['attributes']) && $product['attributes'] != null) ?
                    ' (' . $product['attributes'] . ') ' . '[ID: ' . $product['id_product_attribute'] . ']' : '');
                    $orderDetail->product_attribute_id = $product['id_product_attribute'];
                    $orderDetail->id_order = $order->id;
                    $orderDetail->id_warehouse = $package['id_warehouse'];
                    $orderDetail->unit_price_tax_excl = $product['price'];
                    $orderDetail->product_price = $product['price'];
                    $orderDetail->product_quantity = $product['cart_quantity'];
                    $orderDetail->id_shop = $cart->id_shop;

                    $orderDetail->add();

                    $order_carrier = new OrderCarrier();
                    $order_carrier->id_order = (int)$order->id;
                    $order_carrier->id_carrier = (int)$cart->id_carrier;
                    $order_carrier->weight = (float)$order->getTotalWeight();
                    $order_carrier->shipping_cost_tax_excl = (float)$order->total_shipping_tax_excl;
                    $order_carrier->shipping_cost_tax_incl = (float)$order->total_shipping_tax_incl;
                    $order_carrier->add();
                }
            }
        }
    }

    public static function buildOrderData(Order $order)
    {

        return [
            'code' => $order->reference,
            'coupons' => [],
            'currency' => '',
            'items' => [],
            'shippingMethods' => [],
            'totals' => []
        ];
    }

    // Add an OrderPayment entry related to the Order generated
    public static function createOrderPaymentTransaction(Order $order)
    {
        $orderPayment = new OrderPayment();

        $orderPayment->order_reference = $order->reference;
        $orderPayment->id_currency = $order->id_currency;
        $orderPayment->amount = $order->total_paid;
        $orderPayment->payment_method = 'Qoala Checkout';
        $orderPayment->conversion_rate = 1.00;
        $orderPayment->transaction_id = 0;
        $orderPayment->card_number = 0;
        $orderPayment->card_brand = 0;
        $orderPayment->card_expiration = 0;
        $orderPayment->card_holder = 0;
        $orderPayment->date_add = date('Y-m-d H:i:s');

        $orderPayment->add();
    }
}
