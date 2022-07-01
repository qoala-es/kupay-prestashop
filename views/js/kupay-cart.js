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

(function renderKupayButton() {
    const node = document.createElement("div");
    node.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2.15 2.14"><defs><style>.cls-1{fill:#576ab0;}.cls-2{fill:#4855a1;}.cls-3{fill:#6485c3;}.cls-4{fill:#70a4d9;}.cls-5{fill:#8ccfd6;}</style></defs><title>Recurso 4</title><g id="Capa_2" data-name="Capa 2"><g id="Capa_1-2" data-name="Capa 1"><path class="cls-1" d="M0,1.14V.43C.07.43.09.51.15.51s0,.2,0,.3,0,.06.08,0a.11.11,0,0,1,.1.1l0,0S.2,1,.2,1.07s.06.05.09.07l.44.26a.16.16,0,0,0,.24,0l.11.09S1,1.51,1,1.56l.38.21a.16.16,0,0,0,.07.12s0,.05,0,.07l-.3-.15a1,1,0,0,0-.29-.13S.77,1.62.71,1.63s0-.06,0-.08l-.52-.3C.1,1.23.08,1.15,0,1.14Z"/><path class="cls-2" d="M.86,1.68a5.82,5.82,0,0,1-.53.46H.23A.37.37,0,0,1,0,1.91V1.14c.08,0,.1.09.16.11v.58A.13.13,0,0,0,.23,2c.06,0,.09,0,.12-.05s.24-.19.36-.29S.81,1.67.86,1.68Z"/><path class="cls-3" d="M1.79,2.14c-.1-.08-.24-.1-.34-.18s0-.05,0-.07a.16.16,0,0,1-.07-.12L1.77,2a.15.15,0,0,0,.2,0L2.1,2c0,.1-.13.11-.21.15Z"/><path class="cls-3" d="M.15.51C.09.51.07.43,0,.43V.2C.05.17,0,.1.1.07S.18.13.23.15A.22.22,0,0,0,.15.28Z"/><path class="cls-4" d="M.23.15C.18.13.16.07.1.07a.22.22,0,0,1,.32,0,3.61,3.61,0,0,1,.3.25c.11.12.21.17.35.06s.08.06.09.11l-.1.06s0,0,0,.08l.3.29a.06.06,0,0,0,.09,0L1.53.83s.05.09.11.09c-.14.15-.13.15,0,.31a5.15,5.15,0,0,1,.43.5A.19.19,0,0,1,2.1,2L2,1.93a.08.08,0,0,0,0-.11,5.23,5.23,0,0,0-.54-.61s0,0-.06,0l-.08-.11A1.61,1.61,0,0,0,1,.79C.92.69.83.65.72.73L.66.61s.11,0,0-.09A1.58,1.58,0,0,0,.23.15Z"/><path class="cls-5" d="M1.64.92c-.06,0-.07-.06-.11-.09.12-.16.29-.3.4-.47S2,.27,2,.2s-.11-.05-.18,0L1.16.49S1.1.41,1.07.38A3.3,3.3,0,0,1,1.8,0a.23.23,0,0,1,.29.09.21.21,0,0,1,0,.29A3.17,3.17,0,0,1,1.64.92Z"/><path class="cls-3" d="M.23.86A2.3,2.3,0,0,1,.66.61L.72.73.33,1A.11.11,0,0,0,.23.86Z"/><path class="cls-3" d="M1.36,1.2a3.7,3.7,0,0,1-.28.28L1,1.39l.31-.3Z"/></g></g></svg>
    `;
    node.innerHTML += "COMPRAR EN 1 CLICK"
    node.setAttribute("class", "kupay-buy");
    node.setAttribute("onclick", "kupayCartCheckout()");

    const elementDiv = document.getElementsByClassName("cart-summary")[0];
    if (elementDiv) {
        elementDiv.appendChild(node);
    }
    if (kupay.cartStyles) {
        node.setAttribute("style", kupay.cartStyles);
    }
})();
console.log(kupay);
