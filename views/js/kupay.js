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

function handleMessage(e) {
    // e.data hold the message from child
    if(e.data && e.data.orderConfirmed) {
        window.removeEventListener('message', handleMessage);

        const {cartId, orderId, customerKey} = e.data.prestashopInfo;
        const moduleId = kupay.moduleId;

        console.log(`${window.location.origin}?controller=order-confirmation&id_cart${cartId}&id_module=${moduleId}&id_order=${orderId}&key=${customerKey}`)

        // window.location.href =
        // `${window.location.origin}?controller=order-confirmation&id_cart${cartId}&id_module=${moduleId}&id_order=${orderId}&key=${customerKey}`;

        // Tools::redirectLink(__PS_BASE_URI__ . 'index.php?controller=order-confirmation&id_cart=' . $cart->id .'&id_module='. $this->id .'&id_order=' . $id_order . '&key=' . $customer->secure_key);
    }
}

function kupayRedirectToCheckoutWindow(url){
    window.location.href = url;
}

function kupayBuildIframe(iframeUrl) {
    window.addEventListener('message', handleMessage , false);

    const w = 450;
    const h = 1000;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);

    window.open(iframeUrl, "Kupay Checkout", '_self, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
    
}

function getVariantId(currentUrl) {
    const splitted = currentUrl.split('/');
    let variantSection;

    splitted.forEach(section => {
        if(section.includes('.html')) {
            variantSection = section;
        }
    })

    if(variantSection) {
        const variantId = variantSection.split('-')[1];
        if(Number(variantId)) {
            return variantId;
        } else {
            return null;
        }
    } else {
        console.error('ERROR: Unable to find variant id');
        return undefined;
    }
}

async function kupayPdpCheckout() {

    console.log('prestashop', prestashop);

    const quantity = document.getElementById('quantity_wanted').value;
    const variantId = getVariantId(window.location.href);

    console.log('variantId', variantId);

    let iframeUrl = kupay.iframeUrl;

    iframeUrl += "?appId=" + kupay.appId;
    iframeUrl += "&productId=" + kupay.product.id;
    iframeUrl += "&productName=" + kupay.product.name;
    iframeUrl += "&productPrice=" + kupay.product.price;
    iframeUrl += "&productQuantity=" + quantity;
    iframeUrl += "&productImageUrl=" + '';
    iframeUrl += "&requiresProcessing=" + '1';
    iframeUrl += "&origin=" + 'PDP';
    iframeUrl += "&currency=" + prestashop.currency.iso_code;
    iframeUrl += "&deliveryCost=" + '0';
    iframeUrl += "&variantId=" + variantId;
    iframeUrl += "&prestashop=" + true;

    kupayRedirectToCheckoutWindow(iframeUrl);
}

function kupayCartCheckout() {

    console.log('prestashop', prestashop);

    let iframeUrl = kupay.iframeUrl;

    iframeUrl += "?appId=" + kupay.appId;
    iframeUrl += "&origin=" + prestashop.page.page_name.toUpperCase();
    iframeUrl += "&requiresProcessing=" + true;
    iframeUrl += "&currency=" + prestashop.currency.iso_code;
    iframeUrl += "&deliveryCost=" + '0';
    iframeUrl += "&cartId=" + kupay.cartId;
    iframeUrl += "&cartTotal=" + prestashop.cart.totals.total_including_tax.amount;
    iframeUrl += "&prestashop=" + true;

    kupayRedirectToCheckoutWindow(iframeUrl);
}

// Setter functions for Product parameters on the Quickview Modal
function setIdProductAttribute(attribute) {
    kupay.idProductAttribute = attribute;
}

function setIdProduct(id) {
    kupay.idProduct = id;
}

function setProductName(productName) {
    kupay.productName = productName;
}

// Quickview Modal Checkout
async function kupayPdpQuickviewCheckout() {
    console.log('prestashop', prestashop);

    const quantity = document.getElementById('quantity_wanted').value;

    // Get Product price
    let price = document.getElementsByClassName('current-price-value')[0];
    price = parseFloat(price.innerHTML.trim().substring(1));

    let iframeUrl = kupay.iframeUrl;

    iframeUrl += "?appId=" + kupay.appId;
    iframeUrl += "&productId=" + kupay.idProduct;
    iframeUrl += "&productName=" + kupay.productName;
    iframeUrl += "&productPrice=" + price;
    iframeUrl += "&productQuantity=" + quantity;
    iframeUrl += "&productImageUrl=" + '';
    iframeUrl += "&requiresProcessing=" + '1';
    iframeUrl += "&origin=" + 'PDP';
    iframeUrl += "&currency=" + prestashop.currency.iso_code;
    iframeUrl += "&deliveryCost=" + '0';
    iframeUrl += "&variantId=" + kupay.idProductAttribute;
    iframeUrl += "&prestashop=" + true;

    kupayRedirectToCheckoutWindow(iframeUrl);
}

// Check for completed AJAX Requests
$(document).ajaxComplete(function(event, xhr, settings) {
    controllerUrl = "index.php?controller=product";

    // Check if it's coming from the Product Controller
    if (settings.url.includes(controllerUrl)) {

        let resp = JSON.parse(xhr.responseText);
        // Checks if the Quickview Modal is opened for the first time
        if (resp['quickview_html'] && settings.type == "POST") {
            var idProductAttribute = resp['product']['cache_default_attribute'];
            var productName = resp['product']['name'];
        }
        // Checks if the variation inside the modal has changed
        if (resp['is_quick_view'] && settings.type == "POST") {
            var idProductAttribute = resp['id_product_attribute'];
            var productName = resp['product_title'];
        }

        // Set Product Name and Product Attribute id
        setProductName(productName);
        setIdProductAttribute(idProductAttribute);
    }
});