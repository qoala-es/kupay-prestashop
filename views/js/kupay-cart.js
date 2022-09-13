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
        <svg width="32" height="28" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_2833_3360)">
            <path d="M6.00859 22.3121C4.06677 20.0247 2.94775 17.0462 2.94775 13.8208C2.94775 6.59651 8.80613 0.72168 16.0468 0.72168C23.2711 0.72168 29.1294 6.58006 29.1294 13.8208C29.1294 17.8525 27.3193 21.4399 24.4559 23.8425L6.00859 22.3121Z" fill="white"/>
            <path d="M31.0548 14.594L30.8573 15.0219L30.3307 14.6105C30.018 15.3839 29.5079 16.0257 28.8661 16.4865C28.1256 17.0131 27.1876 17.2928 26.1508 17.2928C26.0027 17.2928 25.8382 17.2928 25.6901 17.2764C25.6242 19.7612 24.9495 21.5385 23.633 22.7233L24.1761 24.0892C24.0774 24.1715 23.9622 24.2538 23.8634 24.3196C21.6912 25.9323 18.9924 26.8867 16.0632 26.8867C12.3441 26.8867 8.92127 25.3728 6.53513 22.8879C6.4364 22.7892 6.35412 22.7069 6.27184 22.6082L7.01236 21.3739C5.74524 19.9423 5.25156 17.984 5.56422 15.5814C4.52749 15.4662 3.67177 15.17 2.99707 14.6763C2.20717 14.1003 1.68058 13.2611 1.40082 12.175L0.824859 12.4876L0.69321 12.0269C0.232439 10.4142 0.396999 8.9002 1.17044 7.666C1.94387 6.41533 3.29328 5.51024 4.98826 5.04947L4.47812 4.72035L5.13636 4.42414C5.59713 4.22667 6.07436 4.12793 6.60096 4.12793C6.78198 4.12793 6.96299 4.14439 7.16047 4.16084C8.82253 4.4406 10.4517 5.69126 11.1758 6.94193C12.4429 6.31659 13.9404 5.98747 15.5202 5.98747C17.7582 5.98747 19.9304 6.64572 21.5431 7.79764C22.3988 6.76091 24.0609 5.75709 25.8217 5.75709C26.0192 5.75709 26.2167 5.77354 26.4141 5.79C26.9572 5.85582 27.4673 6.0533 27.9281 6.33305L28.5534 6.74445L27.9775 6.97484C29.5572 7.71536 30.7585 8.85084 31.3181 10.1838C31.8611 11.5661 31.7788 13.0801 31.0548 14.594Z" fill="#CAD9E5"/>
            <path d="M23.4025 10.6283C26.3481 9.55862 27.7798 13.2777 24.9987 14.5777C24.7354 13.1461 24.0278 11.8625 23.1721 10.6941L23.4025 10.6283Z" fill="#242526"/>
            <path d="M6.43643 13.0145C3.90219 11.1714 6.18959 7.91308 8.82257 9.49287C7.78583 10.5131 7.11113 11.8132 6.61745 13.1626L6.43643 13.0145Z" fill="#242526"/>
            <path d="M11.1756 16.9806C11.9663 16.9806 12.6073 16.3396 12.6073 15.5489C12.6073 14.7582 11.9663 14.1172 11.1756 14.1172C10.3849 14.1172 9.7439 14.7582 9.7439 15.5489C9.7439 16.3396 10.3849 16.9806 11.1756 16.9806Z" fill="#242526"/>
            <path d="M18.4494 19.7936C18.3013 21.5874 16.5899 22.9203 14.9443 22.7887C13.3151 22.657 11.8341 21.0937 11.9822 19.3C12.1303 17.5062 13.8417 15.0707 15.4873 15.2024C17.1165 15.3176 18.5975 17.9835 18.4494 19.7936Z" fill="white"/>
            <path d="M21.1152 16.3712C21.0494 17.1611 20.3582 17.7535 19.5683 17.6877C18.7784 17.6219 18.186 16.9307 18.2518 16.1408C18.3176 15.3509 19.0088 14.7585 19.7987 14.8243C20.5886 14.8902 21.181 15.5978 21.1152 16.3712Z" fill="#242526"/>
            <path d="M15.5859 14.1174C14.0555 14.0022 12.8871 17.1288 12.739 18.8567C12.6074 20.5023 13.4466 21.4568 15.0593 21.5226C16.6391 21.5884 17.6759 20.9137 17.7911 19.2846C17.9227 17.5402 17.067 14.249 15.5859 14.1174Z" fill="#242526"/>
            <path d="M30.9889 15.6147L30.4787 15.2197C30.0673 15.9932 29.4749 16.6185 28.7344 17.0299C27.9609 17.4742 27.0394 17.7046 26.0356 17.6882C25.9204 19.9591 25.2621 21.6541 24.0608 22.8389C24.1431 23.0364 24.3077 23.4478 24.4558 23.8427C24.357 23.925 24.2418 24.0238 24.1431 24.106C24.0444 24.1883 23.9292 24.2706 23.8304 24.3364L23.1393 22.625C24.571 21.4731 25.2786 19.5971 25.2786 16.8653C26.628 17.0957 27.9938 16.7666 28.9483 15.9273C29.5078 15.4501 29.9192 14.7919 30.1167 13.9855L30.6926 14.4298C32.3053 11.0892 30.38 8.34107 27.2698 7.13978C27.1546 7.09041 27.0394 7.05749 26.9242 7.00813C26.9736 6.99167 27.0558 6.95876 27.1381 6.92585C27.3356 6.84356 27.5824 6.74483 27.6812 6.69546C27.385 6.49799 27.0558 6.34988 26.7103 6.2676C24.8178 5.80683 22.4646 6.99167 21.5924 8.35753C18.8113 6.13595 14.1378 5.72455 10.9782 7.4689C10.3858 6.10304 8.49334 4.62199 6.74899 4.55616C6.68317 4.55616 6.61734 4.55616 6.55152 4.55616C6.1072 4.55616 5.66289 4.63845 5.23503 4.81946L5.92619 5.26378C2.42103 5.8562 -0.0638424 8.30816 0.989349 11.9614L1.59823 11.6323C1.7957 12.7678 2.22356 13.6235 2.89826 14.1995C3.63878 14.8577 4.65906 15.1868 5.92619 15.2691C5.46541 18.0666 6.1072 20.0249 7.40724 21.3579L6.53506 22.8883C6.43632 22.7895 6.35404 22.7073 6.27176 22.6085C6.18948 22.5098 6.09075 22.4111 6.00847 22.3123C6.18948 21.9832 6.38696 21.6376 6.48569 21.4731C5.33377 20.0414 4.87299 18.1983 5.08692 15.9603C4.26412 15.8286 3.57296 15.5818 2.98054 15.2197C2.09191 14.6767 1.46658 13.8539 1.10454 12.7842L0.528578 13.0969C-0.853736 9.69047 0.528578 6.18532 4.05019 4.98402L3.62233 4.70427C4.47805 4.34224 5.64643 3.78273 6.55152 3.79918C6.86418 3.79918 7.17685 3.8321 7.47306 3.89792C9.0693 4.21059 10.501 5.31315 11.2909 6.46507C14.3517 5.0663 18.729 5.47771 21.4443 7.30434C22.7443 5.97139 24.4064 5.31315 26.0685 5.44479C27.1875 5.52708 28.323 5.98785 29.3597 6.86002L28.866 7.05749C32.1243 8.83476 32.9142 12.488 30.9889 15.6147Z" fill="#242526"/>
            </g>
            <defs>
            <clipPath id="clip0_2833_3360">
            <rect width="32" height="27.678" fill="white"/>
            </clipPath>
            </defs>
        </svg>
    `;
    node.innerHTML += "Comprar ahora"
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
