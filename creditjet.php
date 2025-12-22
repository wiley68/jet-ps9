<?php

/**
 * @File: creditjet.php
 * @Author: Ilko Ivanov
 * @Author e-mail: ilko.iv@gmail.com
 * @Publisher: Avalon Ltd
 * @Publisher e-mail: home@avalonbg.com
 * @Owner: Avalon Ltd
 * @Version: 1.8.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

// Module constants are now defined as class constants above

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

/**
 * CreditJet Payment Module for PrestaShop 9.x
 * PB Personal Finance Credit Calculator
 *
 * This module provides credit payment options for customers using PB Personal Finance services.
 * It allows customers to purchase products on installments with credit card payments.
 *
 * @property int $is_eu_compatible Whether the module is EU compatible
 * @property array|string $currencies Supported currencies for the module
 * @property string $currencies_mode Currency selection mode
 */
class Creditjet extends PaymentModule
{
    // Module constants

    /**
     * Minimum price for credit calculations
     */
    const JET_MINPRICE = 150.00;

    /**
     * Minimum amount for 250 BGN credit threshold
     */
    const JET_MIN_250 = 250.00;

    /**
     * Minimum amount for 250 EUR credit threshold (converted)
     */
    const JET_MIN_250_EUR = 125.00;

    /**
     * Maximum iterations for financial calculations
     */
    const JETCREDIT_FINANCIAL_MAX_ITERATIONS = 128;

    /**
     * Precision for financial calculations
     */
    const JETCREDIT_FINANCIAL_PRECISION = 1.0e-08;

    /**
     * Display name for card payment option
     *
     * @var string
     */
    public $displayName_card;

    /**
     * Check if current user is on mobile device
     *
     * This method detects mobile devices based on user agent string patterns
     * to provide appropriate styling and functionality for mobile users.
     *
     * @return bool True if mobile device detected, false otherwise
     */
    public static function isMobileDevice(): bool
    {
        $useragent = array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : '';
        return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent)
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));
    }

    /**
     * List of hooks that this module registers
     */
    const HOOKS = [
        'ActionFrontControllerSetMedia',
        'displayProductAdditionalInfo',
        'displayShoppingCart',
        'paymentOptions'
    ];

    /**
     * Constructor
     *
     * Initializes the module with configuration settings and checks for required currencies.
     * Sets up module properties and validates system requirements.
     */
    public function __construct()
    {
        $this->name = 'creditjet';
        $this->tab = 'payments_gateways';
        $this->version = '1.8.0';
        $this->author = 'Ilko Ivanov';
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => '9.99.99'];
        $this->bootstrap = true;
        $this->is_eu_compatible = 1;
        $this->currencies = ['EUR', 'BGN'];
        $this->currencies_mode = 'checkbox';

        parent::__construct();

        $this->displayName = $this->l('PB Personal Finance');
        $this->displayName_card = $this->l('PB Personal Finance - in installments with a credit card');
        $this->description = $this->l('Allows your customers to purchase goods on installments with PB Personal Finance');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        // Check if required currencies (EUR, BGN) are active in the system
        $eurCurrency = Currency::getIdByIsoCode('EUR');
        $bgnCurrency = Currency::getIdByIsoCode('BGN');

        if ((!$eurCurrency && !$bgnCurrency) && $this->active) {
            $this->warning = $this->l('EUR or BGN currency must be enabled in the system.');
        }

        if (!Configuration::get('CREDITJET_NAME')) {
            $this->warning = $this->l('No merchant ID provided');
        }
    }

    /**
     * Install the module
     *
     * Creates database tables, registers hooks, and sets default configuration values.
     *
     * @return bool True if installation was successful, false otherwise
     */
    public function install(): bool
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHooks()
            && Configuration::updateValue(
                'CREDITJET_NAME',
                'PB Personal Finance'
            )
            && Configuration::updateValue(
                'JET_EMAIL',
                'online.shop@pbpf.bg'
            )
            && Configuration::updateValue(
                'JET_PURCENT',
                1.40
            )
            && Configuration::updateValue(
                'JET_VNOSKI_DEFAULT',
                12
            )
            && Configuration::updateValue(
                'JET_PURCENT_CARD',
                1.40
            )
            && Configuration::updateValue(
                'JET_GAP',
                0
            )
            && Configuration::updateValue(
                'JET_MINPRICE',
                150
            );
    }

    /**
     * Register module hooks
     *
     * Registers all hooks defined in the HOOKS constant.
     *
     * @return bool True if all hooks were registered successfully, false otherwise
     */
    private function registerHooks(): bool
    {
        foreach (static::HOOKS as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Uninstall the module
     *
     * Removes database tables and configuration values created during installation.
     *
     * @return bool True if uninstallation was successful, false otherwise
     */
    public function uninstall(): bool
    {
        return $this->uninstallDb()
            && Configuration::deleteByName('CREDITJET_NAME')
            && Configuration::deleteByName('PS_OS_CREDITJET')
            && Configuration::deleteByName('JET_STATUS_IN')
            && Configuration::deleteByName('JET_EMAIL')
            && Configuration::deleteByName('JET_ID')
            && Configuration::deleteByName('JET_PURCENT')
            && Configuration::deleteByName('JET_VNOSKI_DEFAULT')
            && Configuration::deleteByName('JET_CART_SHOW')
            && Configuration::deleteByName('JET_CARD_IN')
            && Configuration::deleteByName('JET_PURCENT_CARD')
            && Configuration::deleteByName('JET_COUNT')
            && Configuration::deleteByName('JET_GAP')
            && Configuration::deleteByName('JET_Z1')
            && Configuration::deleteByName('JET_VNOSKA')
            && Configuration::deleteByName('JET_MINPRICE')
            && Configuration::deleteByName('JET_EUR')
            && parent::uninstall();
    }

    /**
     * Install database tables
     *
     * Executes SQL queries from the install.sql file to create required database tables.
     *
     * @return bool True if all queries executed successfully, false otherwise
     */
    private function installDb(): bool
    {
        $sql = file_get_contents(__DIR__ . '/sql/install.sql');
        $sql = str_replace('ps_', _DB_PREFIX_, $sql);

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Uninstall database tables
     *
     * Executes SQL queries from the uninstall.sql file to remove database tables.
     *
     * @return bool True if all queries executed successfully, false otherwise
     */
    private function uninstallDb(): bool
    {
        $sql = file_get_contents(__DIR__ . '/sql/uninstall.sql');
        $sql = str_replace('ps_', _DB_PREFIX_, $sql);

        foreach (array_filter(array_map('trim', explode(';', $sql))) as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the module uses the new translation system
     *
     * @return bool Always returns true for this module
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * Get module configuration content
     *
     * Redirects to the module configuration form in the admin panel.
     *
     * @return string Empty string (redirects via Tools::redirectAdmin)
     */
    public function getContent(): string
    {
        $router = SymfonyContainer::getInstance()->get('router');
        $url = $router->generate('credit_jet_configuration_form');

        Tools::redirectAdmin($url);
        return '';
    }

    /**
     * Hook for adding CSS and JS files to frontend pages
     *
     * Registers module-specific CSS and JavaScript files for product, cart, and order pages.
     *
     * @param array $params Hook parameters
     *
     * @return void
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        if ('product' === $this->context->controller->php_self) {
            $this->context->controller->registerJavascript(
                'module-creditjet-product-js',
                'modules/' . $this->name . '/js/creditjet_product.js',
                [
                    'priority' => 200,
                    'attribute' => 'async',
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/js/creditjet_product.js')
                ]
            );
            $this->context->controller->registerStylesheet(
                'module-creditjet-product-css',
                'modules/' . $this->name . '/css/creditjet_product.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/css/creditjet_product.css')
                ]
            );
        }
        if ('cart' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet(
                'module-creditjet-cart-css',
                'modules/' . $this->name . '/css/creditjet_cart.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/css/creditjet_cart.css')
                ]
            );
            $this->context->controller->registerJavascript(
                'module-creditjet-cart-js',
                'modules/' . $this->name . '/js/creditjet_cart.js',
                [
                    'priority' => 200,
                    'attribute' => 'async',
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/js/creditjet_cart.js')
                ]
            );
        }
        if ('order' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet(
                'module-creditjet-checkout-css',
                'modules/' . $this->name . '/css/creditjet_checkout.css',
                [
                    'media' => 'all',
                    'priority' => 200,
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/css/creditjet_checkout.css')
                ]
            );
            $this->context->controller->registerJavascript(
                'module-creditjet-checkout-js',
                'modules/' . $this->name . '/js/creditjet_checkout.js',
                [
                    'priority' => 200,
                    'attribute' => 'async',
                    'version' => filemtime(_PS_MODULE_DIR_ . $this->name . '/js/creditjet_checkout.js')
                ]
            );
        }
    }

    /**
     * Hook for displaying additional product information
     *
     * Displays credit calculator widget on product pages with installment options.
     *
     * @param array $params Hook parameters containing product information
     *
     * @return string|null HTML content for the credit calculator or null if not applicable
     */
    public function hookDisplayProductAdditionalInfo($params)
    {
        // Validate params
        if (!isset($params['product']) || !$params['product']) {
            return null;
        }

        $jet_id_product = (int) $params['product']->id;
        $jet_price = (float)Product::getPriceStatic($jet_id_product, true);
        $jet_price_before = $jet_price;

        // Validate currency context
        if (!isset($this->context->currency) || !$this->context->currency) {
            return null;
        }

        $jet_currency_code = $this->context->currency->iso_code;
        if ($jet_currency_code != 'EUR' && $jet_currency_code != 'BGN') {
            return null;
        }

        $jet_status = (int)Configuration::get('JET_STATUS_IN');
        if ($jet_status == 0) {
            return null;
        }

        $jet_eur = (int)Configuration::get("JET_EUR");
        $jet_sign = 'лв.';
        $jet_sign_second = 'евро';
        $jet_min_250 = self::JET_MIN_250;

        switch ($jet_eur) {
            case 0:
                $jet_sign = 'лв.';
                $jet_sign_second = '';
                break;
            case 1:
                if ($jet_currency_code == "EUR") {
                    $jet_price = $jet_price * 1.95583;
                }
                $jet_sign = 'лв.';
                $jet_sign_second = 'евро';
                break;
            case 2:
                if ($jet_currency_code == "BGN") {
                    $jet_price = $jet_price / 1.95583;
                }
                $jet_sign = 'евро';
                $jet_sign_second = 'лв.';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
            case 3:
                if ($jet_currency_code == "BGN") {
                    $jet_price = $jet_price / 1.95583;
                }
                $jet_sign = 'евро';
                $jet_sign_second = '';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
        }

        $jet_gap = (int)Configuration::get("JET_GAP");
        $zaiavka1jet_text = Configuration::get("JET_Z1");
        $jet_vnoski_default = (int)Configuration::get("JET_VNOSKI_DEFAULT");
        if ($jet_price < $jet_min_250) {
            $jet_vnoski = '9';
        } else {
            $jet_vnoski = $jet_vnoski_default;
        }

        $jet_card_in = (int)Configuration::get("JET_CARD_IN");
        $is_vnoska = (int)Configuration::get("JET_VNOSKA");

        $jet_name = "";
        $jet_lastname = "";
        $jet_email = "";
        $jet_phone = "";
        $jetCustomer = $this->context->customer;
        if ($jetCustomer->isLogged()) {
            $jet_name = $jetCustomer->firstname;
            $jet_lastname = $jetCustomer->lastname;
            $jet_email = $jetCustomer->email;
            $jet_phone = $jetCustomer->getAddresses($this->context->language->id)[0]['phone'];
        }

        if (self::isMobileDevice()) {
            $jet_PopUp_Detailed_v1 = "jetm_PopUp_Detailed_v1";
            $jet_Mask = "jetm_Mask";
            $jet_column_left = "jetm_column_left";
            $jet_column_right = "jetm_column_right";
            $obshto_credit_text = 'Общо кредит';
            $obshto_plashtania = 'Общо плащания';
            $modalpayment_jet = 'modalpayment_jetm';
        } else {
            $jet_PopUp_Detailed_v1 = "jet_PopUp_Detailed_v1";
            $jet_Mask = "jet_Mask";
            $jet_column_left = "jet_column_left";
            $jet_column_right = "jet_column_right";
            $obshto_credit_text = 'Общ размер на кредита';
            $obshto_plashtania = 'Обща стойност на плащанията';
            $modalpayment_jet = 'modalpayment_jet';
        }

        $this->context->smarty->assign([
            'modules_dir' => $this->context->link->getBaseLink() . 'modules/',
            'static_token' => \Tools::getToken(),
            'jet_gap' => $jet_gap,
            'jet_price' => number_format($jet_price_before, 2, '.', ''),
            'jet_card_in' => $jet_card_in,
            'zaiavka1jet_text' => $zaiavka1jet_text,
            'is_vnoska' => $is_vnoska,
            'jet_sign_second' => $jet_sign_second,
            'jet_sign' => $jet_sign,
            'jet_id_product' => $jet_id_product,
            'modalpayment_jet' => $modalpayment_jet,
            'jet_PopUp_Detailed_v1' => $jet_PopUp_Detailed_v1,
            'jet_Mask' => $jet_Mask,
            'jet_column_left' => $jet_column_left,
            'jet_column_right' => $jet_column_right,
            'obshto_credit_text' => $obshto_credit_text,
            'obshto_plashtania' => $obshto_plashtania,
            'jet_eur' => $jet_eur,
            'jet_vnoski' => $jet_vnoski,
            'jet_name' => $jet_name,
            'jet_lastname' => $jet_lastname,
            'jet_email' => $jet_email,
            'jet_phone' => $jet_phone
        ]);

        return $this->fetch('module:creditjet/views/templates/hook/creditjet.tpl');
    }

    /**
     * Hook for displaying shopping cart information
     *
     * Displays credit calculator widget on shopping cart page with cart total calculations.
     *
     * @param array $params Hook parameters containing cart information
     *
     * @return string|null HTML content for the credit calculator or null if not applicable
     */
    public function hookDisplayShoppingCart($params)
    {
        // Validate currency context
        if (!isset($this->context->currency) || !$this->context->currency) {
            return null;
        }

        $jet_currency_code = $this->context->currency->iso_code;
        if ($jet_currency_code != 'EUR' && $jet_currency_code != 'BGN') {
            return null;
        }

        $jet_status = (int)Configuration::get('JET_STATUS_IN');
        if ($jet_status == 0) {
            return null;
        }

        $jet_cart_show = (int)Configuration::get("JET_CART_SHOW");
        if ($jet_cart_show == 0) {
            return null;
        }

        $zaiavka1jet_text = Configuration::get("JET_Z1");
        $jet_card_in = (int)Configuration::get("JET_CARD_IN");
        $is_vnoska = (int)Configuration::get("JET_VNOSKA");

        $jet_products = $params['cart']->getProducts(true);
        $jet_price = $params['cart']->getordertotal(true);
        $jet_price_before = $jet_price;

        $jet_eur = (int)Configuration::get("JET_EUR");
        $jet_sign = 'лв.';
        $jet_sign_second = 'евро';
        $jet_min_250 = self::JET_MIN_250;

        switch ($jet_eur) {
            case 0:
                $jet_sign = 'лв.';
                $jet_sign_second = '';
                break;
            case 1:
                if ($jet_currency_code == "EUR") {
                    $jet_price = $jet_price * 1.95583;
                }
                $jet_sign = 'лв.';
                $jet_sign_second = 'евро';
                break;
            case 2:
                if ($jet_currency_code == "BGN") {
                    $jet_price = $jet_price / 1.95583;
                }
                $jet_sign = 'евро';
                $jet_sign_second = 'лв.';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
            case 3:
                if ($jet_currency_code == "BGN") {
                    $jet_price = $jet_price / 1.95583;
                }
                $jet_sign = 'евро';
                $jet_sign_second = '';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
        }

        $_minprice = (float)Configuration::get("JET_MINPRICE");
        if ($jet_price < $_minprice) {
            return null;
        }

        $jet_vnoski_default = (int)Configuration::get("JET_VNOSKI_DEFAULT");
        if ($jet_price < $jet_min_250) {
            $jet_vnoski = '9';
        } else {
            $jet_vnoski = $jet_vnoski_default;
        }

        $jet_name = "";
        $jet_lastname = "";
        $jet_email = "";
        $jet_phone = "";
        $jetCustomer = $this->context->customer;
        if ($jetCustomer->isLogged()) {
            $jet_name = $jetCustomer->firstname;
            $jet_lastname = $jetCustomer->lastname;
            $jet_email = $jetCustomer->email;
            $jet_phone = $jetCustomer->getAddresses($this->context->language->id)[0]['phone'];
        }

        if (self::isMobileDevice()) {
            $jet_PopUp_Detailed_v1 = "jetm_PopUp_Detailed_v1";
            $jet_Mask = "jetm_Mask";
            $jet_column_left = "jetm_column_left";
            $jet_column_right = "jetm_column_right";
            $obshto_credit_text = 'Общо кредит';
            $obshto_plashtania = 'Общо плащания';
            $modalpayment_jet = 'modalpayment_jetm';
        } else {
            $jet_PopUp_Detailed_v1 = "jet_PopUp_Detailed_v1";
            $jet_Mask = "jet_Mask";
            $jet_column_left = "jet_column_left";
            $jet_column_right = "jet_column_right";
            $obshto_credit_text = 'Общ размер на кредита';
            $obshto_plashtania = 'Обща стойност на плащанията';
            $modalpayment_jet = 'modalpayment_jet';
        }

        $jet_products_id = '';
        $jet_products_qt = '';
        $jet_products_pr = '';
        $jet_products_ct = '';
        $jet_products_vr = '';
        foreach ($jet_products as $product) {
            $jet_products_id .= $product['id_product'] . '_';
            $jet_products_qt .= $product['cart_quantity'] . '_';
            $jet_products_pr_current = (float)$product['price_wt'];
            switch ($jet_eur) {
                case 0:
                    break;
                case 1:
                    if ($jet_currency_code == "EUR") {
                        $jet_products_pr_current = $jet_products_pr_current * 1.95583;
                    }
                    break;
                case 2:
                case 3:
                    if ($jet_currency_code == "BGN") {
                        $jet_products_pr_current = $jet_products_pr_current / 1.95583;
                    }
                    break;
            }
            $jet_products_pr .= number_format($jet_products_pr_current, 2, ".", "") . '_';
            $jet_products_ct .= $product['id_category_default'] . '_';
            $jet_products_vr .= $product['id_product_attribute'] . '_';
        }
        $jet_products_id = substr($jet_products_id, 0, -1);
        $jet_products_qt = substr($jet_products_qt, 0, -1);
        $jet_products_pr = substr($jet_products_pr, 0, -1);
        $jet_products_ct = substr($jet_products_ct, 0, -1);
        $jet_products_vr = substr($jet_products_vr, 0, -1);

        $this->context->smarty->assign([
            'modules_dir' => $this->context->link->getBaseLink() . 'modules/',
            'static_token' => \Tools::getToken(),
            'jet_price' => number_format($jet_price_before, 2, '.', ''),
            'jet_card_in' => $jet_card_in,
            'jet_products_id' => $jet_products_id,
            'jet_products_qt' => $jet_products_qt,
            'jet_products_pr' => $jet_products_pr,
            'jet_products_ct' => $jet_products_ct,
            'jet_products_vr' => $jet_products_vr,
            'zaiavka1jet_text' => $zaiavka1jet_text,
            'is_vnoska' => $is_vnoska,
            'jet_sign_second' => $jet_sign_second,
            'jet_sign' => $jet_sign,
            'modalpayment_jet' => $modalpayment_jet,
            'jet_PopUp_Detailed_v1' => $jet_PopUp_Detailed_v1,
            'jet_Mask' => $jet_Mask,
            'jet_column_left' => $jet_column_left,
            'jet_column_right' => $jet_column_right,
            'jet_eur' => $jet_eur,
            'jet_vnoski' => $jet_vnoski,
            'obshto_credit_text' => $obshto_credit_text,
            'obshto_plashtania' => $obshto_plashtania,
            'jet_name' => $jet_name,
            'jet_lastname' => $jet_lastname,
            'jet_phone' => $jet_phone,
            'jet_email' => $jet_email
        ]);

        return $this->fetch('module:creditjet/views/templates/hook/shoppingbag.tpl');
    }

    /**
     * Hook for adding payment options during checkout
     *
     * Creates payment options for credit payment and optional credit card payment
     * based on module configuration and cart contents.
     *
     * @param array $params Hook parameters containing cart information
     *
     * @return array Array of PaymentOption objects or empty array if not applicable
     */
    public function hookPaymentOptions($params)
    {
        if (empty($params['cart'])) {
            return [];
        }

        $cart = $params['cart'];

        if ($cart->isVirtualCart()) {
            return [];
        }

        $jet_currency_code = $this->context->currency->iso_code;
        if ($jet_currency_code != 'EUR' && $jet_currency_code != 'BGN') {
            return [];
        }

        $jet_status_in = (int)Configuration::get('JET_STATUS_IN');
        $jet_card_in = (int)Configuration::get('JET_CARD_IN');

        if ($jet_status_in == 0) {
            return [];
        }

        $jet_price = $cart->getordertotal(true);

        $jet_eur = (int)Configuration::get("JET_EUR");
        $jet_sign = 'лв.';
        $jet_sign_second = 'евро';
        $jet_min_250 = self::JET_MIN_250;

        switch ($jet_eur) {
            case 0:
                $jet_sign = 'лв.';
                $jet_sign_second = '';
                break;
            case 1:
                if ($jet_currency_code == "EUR") {
                    $jet_price = number_format($jet_price * 1.95583, 2, ".", "");
                }
                $jet_sign = 'лв.';
                $jet_sign_second = 'евро';
                break;
            case 2:
                if ($jet_currency_code == "BGN") {
                    $jet_price = number_format($jet_price / 1.95583, 2, ".", "");
                }
                $jet_sign = 'евро';
                $jet_sign_second = 'лв.';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
            case 3:
                if ($jet_currency_code == "BGN") {
                    $jet_price = number_format($jet_price / 1.95583, 2, ".", "");
                }
                $jet_sign = 'евро';
                $jet_sign_second = '';
                $jet_min_250 = self::JET_MIN_250_EUR;
                break;
        }

        $jet_vnoski_default = (int)Configuration::get("JET_VNOSKI_DEFAULT");
        if ($jet_price < $jet_min_250) {
            $jet_vnoski = '9';
        } else {
            $jet_vnoski = $jet_vnoski_default;
        }

        $jet_products = $cart->getProducts(true);
        $jet_products_id = '';
        $jet_products_qt = '';
        $jet_products_pr = '';
        $jet_products_ct = '';
        $jet_products_vr = '';
        foreach ($jet_products as $product) {
            $jet_products_id .= $product['id_product'] . '_';
            $jet_products_qt .= $product['cart_quantity'] . '_';
            $jet_products_pr_current = (float)$product['price_wt'];
            switch ($jet_eur) {
                case 0:
                    break;
                case 1:
                    if ($jet_currency_code == "EUR") {
                        $jet_products_pr_current = $jet_products_pr_current * 1.95583;
                    }
                    break;
                case 2:
                case 3:
                    if ($jet_currency_code == "BGN") {
                        $jet_products_pr_current = $jet_products_pr_current / 1.95583;
                    }
                    break;
            }
            $jet_products_pr .= number_format($jet_products_pr_current, 2, ".", "") . '_';
            $jet_products_ct .= $product['id_category_default'] . '_';
            $jet_products_vr .= $product['id_product_attribute'] . '_';
        }
        $jet_products_id = substr($jet_products_id, 0, -1);
        $jet_products_qt = substr($jet_products_qt, 0, -1);
        $jet_products_pr = substr($jet_products_pr, 0, -1);
        $jet_products_ct = substr($jet_products_ct, 0, -1);
        $jet_products_vr = substr($jet_products_vr, 0, -1);

        if (self::isMobileDevice()) {
            $jet_column_left = "jetm_column_left";
            $jet_column_right = "jetm_column_right";
        } else {
            $jet_column_left = "jet_column_left";
            $jet_column_right = "jet_column_right";
        }

        $this->context->smarty->assign([
            'static_token' => \Tools::getToken(),
            'static_token_card' => \Tools::getToken(),
            'jet_price' => $cart->getordertotal(true),
            'jet_products_id' => $jet_products_id,
            'jet_products_qt' => $jet_products_qt,
            'jet_products_pr' => $jet_products_pr,
            'jet_products_ct' => $jet_products_ct,
            'jet_products_vr' => $jet_products_vr,
            'jet_column_left' => $jet_column_left,
            'jet_sign' => $jet_sign,
            'jet_column_right' => $jet_column_right,
            'jet_sign_second' => $jet_sign_second,
            'jet_eur' => $jet_eur,
            'jet_vnoski' => $jet_vnoski,
            'jet_action' => $this->context->link->getModuleLink($this->name, 'validation', ['jet_card' => 0], true),
            'jet_action_card' => $this->context->link->getModuleLink($this->name, 'validation', ['jet_card' => 1], true)
        ]);

        $payment_options = [];

        $newOption_JET = new PaymentOption();
        $newOption_JET->setModuleName($this->name);
        $newOption_JET->setCallToActionText($this->displayName);
        $newOption_JET->setAdditionalInformation($this->fetch('module:creditjet/views/templates/hook/jet_checkout.tpl'));
        $payment_options[] = $newOption_JET;

        if ($jet_card_in == 1) {
            $newOption_JET_CARD = new PaymentOption();
            $newOption_JET_CARD->setModuleName($this->name . 'card');
            $newOption_JET_CARD->setCallToActionText($this->displayName_card);
            $newOption_JET_CARD->setAdditionalInformation($this->fetch('module:creditjet/views/templates/hook/jet_checkout_card.tpl'));
            $payment_options[] = $newOption_JET_CARD;
        }

        return $payment_options;
    }
}
