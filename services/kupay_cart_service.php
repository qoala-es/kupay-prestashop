<?php

use PrestaShopBundle\Entity\Lang;

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
 * @author    Kupayco
 * @copyright 2021 Kupay.co
 * @license   GPLv2 or later
 * @license-file license.txt
 */

require_once dirname(__FILE__) . '../../services/kupay_log_service.php';
class KupayCartService
{

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function create(Customer $customer, $payload): array
    {

        // $shops = Shop::getShops();

        $currency_id = Currency::getIdByIsoCode($payload['currency']);
        $lang_id = Language::getIdByIso($payload['shopper']['lang']);

        $cartCode = null;
        if ($payload['origin'] === 'CART' || $payload['origin'] === 'CHECKOUT') {
            $cartCode = $payload['code'];
        }

        $cart = new Cart($cartCode);
        $cart->id_customer = $customer->id;
        $cart->id_lang = $lang_id;
        $cart->id_currency = $currency_id;
        $cart->id_address_delivery = self::getCustomerDeliveryAddress($customer);
        $cart->id_address_invoice = self::getCustomerDeliveryAddress($customer);

        $cart->id_shop = 1;

        if ($payload['origin'] === 'CART' || $payload['origin'] === 'CHECKOUT') {
            $cart->update();
        } else {
            $cart->add();
        }

        self::addProducts($cart, $payload);
        self::addCoupons($cart, $payload, $lang_id);

        $cartData = self::buildCartData($cart, $payload);

        KupayLogService::logNewRelic("INFO", "Cart (ID: $cart->id) Create", "cart");

        return $cartData;
    }

    /**
     * @throws PrestaShopException
     */
    public static function update(Customer $customer, $payload): array
    {

        $lang_id = Language::getIdByIso($payload['shopper']['lang']);

        $cart = new Cart($payload['code']);

        self::addCoupons($cart, $payload, $lang_id);
        self::updateShippingMethod($cart, $payload);

        $cartData = self::updateCartData($cart, $payload);

        KupayLogService::logNewRelic("INFO", "Cart (ID: $cart->id) Update", "cart");

        return $cartData;
    }

    /**
     * @throws PrestaShopException
     */
    public static function retrieve($cart): array
    {
        $cart = new Cart($cart);
        $payload = [];

        return self::buildCartData($cart, $payload);
    }

    /**
     * @throws PrestaShopException
     */
    public static function updateShippingMethod(Cart $cart, $payload): void
    {
        foreach ($payload['shippingMethods'] as $shippingMethod) {
            if ($shippingMethod['isSelected']) {
                $cart->setDeliveryOption([$cart->id_address_delivery => (int)$shippingMethod['code'] . ',']);
                $cart->save();
            }
        }
    }

    public static function getCustomerDeliveryAddress(Customer $customer)
    {

        $addresses = $customer->getAddresses($customer->id_lang);
        return $addresses[0]["id_address"];
    }

    public static function addProducts(Cart $cart, $payload): void
    {

        foreach ($payload['items'] as $item) {

            if (!empty($item['variantId'])) {
                $cart->updateQty($item['quantity'], $item['code'], $item['variantId']);
            } else {
                $cart->updateQty($item['quantity'], $item['code']);
            }
        }
    }

    /**
     * @throws PrestaShopException
     */
    public static function addCoupons(Cart $cart, $payload, $lang_id): void
    {

        if (isset($payload['coupons'])) {

            foreach ($payload['coupons'] as $coupon) {

                $cartRule = CartRule::getCartsRuleByCode($coupon['code'], $lang_id);
                $cart->addCartRule((int) $cartRule);
                $cart->save();
            }
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    private static function updateCartData(Cart $cart, $payload): array
    {

        return [
            'code' => (string) $cart->id,
            'origin' => $payload['origin'],
            'shopper' => $payload['shopper'],
            'items' => self::getCartProducts($cart),
            'shippingMethods' => $payload['shippingMethods'],
            'coupons' => self::getCartCoupons($cart),
            'totals' => self::getCartTotals($cart)
        ];
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws Exception
     */
    private static function buildCartData(Cart $cart, $payload): array
    {
        $country = new Country(Country::getByIso($payload['shopper']['shippingAddress']['countryCode']));

        return [
            'code' => (string) $cart->id,
            'origin' => $payload['origin'],
            'shopper' => $payload['shopper'],
            'items' => self::getCartProducts($cart),
            'shippingMethods' => self::getCartShippingMethods($cart, $country),
            'coupons' => self::getCartCoupons($cart),
            'totals' => self::getCartTotals($cart)
        ];
    }

    /*
    * Todo: to work selected carrier
    */
    private static function getCartShippingMethods(Cart $cart, Country $country): array
    {

        $deliveryOptionsList = $cart->getDeliveryOptionList($country);

        $deliveryOptions = [];

        foreach ($deliveryOptionsList as $deliveryOption) {

            foreach ($deliveryOption as $option) {

                $carrierList = $option['carrier_list'];

                foreach ($carrierList as $key => $carrier) {

                    $deliveryOptions[] = [
                        'name' => $carrier['instance']->name,
                        'code' => (string) $key,
                        'subtotal' => number_format($carrier['price_without_tax'], 2),
                        'tax' => number_format((float)$carrier['price_with_tax'] - (float)$carrier['price_without_tax'], 2),
                        'total' => number_format((float)$carrier['price_with_tax'], 2),
                        'isSelected' => (bool) $cart->id_carrier == $carrier['instance']->id_reference
                    ];
                }
            }
        }

        $deliveryOptions[0]['isSelected'] = true;

        return $deliveryOptions;
    }

    private static function getCartProducts(Cart $cart): array
    {

        $items = [];

        foreach ($cart->getProducts() as $product) {

            $items[] = [

                'code' => (string) $product['id_product'],
                'quantity' => (int) $product['cart_quantity'],
                'variantId' => (string) $product['id_product_attribute'],
                'name' => $product['name'] .
                    ((isset($product['attributes']) && $product['attributes'] != null) ?
                        ' (' . $product['attributes'] . ') ' : ''),
                'price' => (float) number_format($product['price'], 2),
                // 'imageUrl' => self::getProductImage($product['id_product']),
                'imageUrl' => self::getProductAttributeImage($product['id_product'], $product['id_product_attribute'])

            ];
        }

        return $items;
    }

    // Retrieve the image url of the product
    private static function getProductAttributeImage($id_product, $id_product_attribute)
    {
        try {
            $get_cover = false;
            $product = new Product((int)$id_product);

            // If there is no variant ID, go for the product's cover image
            if ($id_product_attribute == 0) {
                $get_cover = true;
            }

            // Set image name
            $image_name = $product->link_rewrite[count($product->link_rewrite) - 1];
            // Get id_image from the id_product_attribute
            $image_attribute = $product->_getAttributeImageAssociations($id_product_attribute);

            // If the product does not have a image related to the attribute selected, go for the cover image
            if (count($image_attribute) > 0) {
                return Context::getContext()->link->getImageLink($image_name, (int)$image_attribute[0], "medium_default");
            } else {
                $get_cover = true;
            }

            if ($get_cover) {
                $image = $product->getCover($product->id);
                return Context::getContext()->link->getImageLink($image_name, (int)$image['id_image'], "medium_default");
            }
        } catch (Exception $e) {

            return "https://user-images.githubusercontent.com/101482/29592647-40da86ca-875a-11e7-8bc3-941700b0a323.png";
        }
    }

    public static function calculateCartShipping(Cart $cart): float
    {

        return $cart->getTotalShippingCost();
    }

    public static function calculateProductsTotal(Cart $cart): float
    {

        $total = 0.0;

        foreach ($cart->getProducts() as $product) {
            $total += (float)$product['price'];
        }

        return $total;
    }

    public static function getCartCoupons(Cart $cart): array
    {

        $coupons = [];

        foreach ($cart->getCartRules() as $rule) {
            $coupons[] = [
                'code' => (string) $rule['code'],
                'value' => (float) $rule['value_real']
            ];
        }

        return $coupons;
    }

    /**
     * @throws Exception
     */
    public static function getCartTotals(Cart $cart): array
    {

        $cartAmountTaxIncluded = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $cartAmountTaxExcluded = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        return [
            'tax' => number_format($cartAmountTaxIncluded - $cartAmountTaxExcluded, 2),
            'shipping' => number_format(self::calculateCartShipping($cart), 2),
            'subtotal' => number_format(self::calculateProductsTotal($cart), 2),
            'discounts' => number_format($cart->getDiscountSubtotalWithoutGifts(true), 2),
            'total' => number_format($cart->getOrderTotal(true, Cart::BOTH), 2),
        ];
    }
}
