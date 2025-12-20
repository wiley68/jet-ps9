<?php

/**
 * PrestaShop Core Classes Stubs for Intelephense
 * These stubs help avoid false error messages in the editor
 */

// Core classes
class Module
{
    public $name;
    public $tab;
    public $version;
    public $author;
    public $need_instance;
    public $ps_versions_compliancy;
    public $bootstrap;
    public $displayName;
    public $description;
    public $confirmUninstall;
    public $warning;
    public $context;
    public $smarty;

    public function __construct() {}
    public function install(): bool
    {
        return true;
    }
    public function uninstall(): bool
    {
        return true;
    }
    public function registerHook(string $hook_name): bool
    {
        return true;
    }
    public function unregisterHook(string $hook_name): bool
    {
        return true;
    }
    public function getContent(): string
    {
        return '';
    }
    public function l(string $string): string
    {
        return $string;
    }
    public function displayConfirmation(string $message): string
    {
        return '';
    }
    public function displayError(string $message): string
    {
        return '';
    }
}

class PaymentModule extends Module
{
    public function checkCurrency($currency): bool
    {
        return true;
    }
}

class PaymentOption
{
    public function setCallToActionText(string $text): self
    {
        return $this;
    }
    public function setAction(string $action): self
    {
        return $this;
    }
    public function setAdditionalInformation(string $info): self
    {
        return $this;
    }
    public function setLogo(string $logo): self
    {
        return $this;
    }
}

class HelperForm
{
    public $show_toolbar = true;
    public $table;
    public $module;
    public $default_form_language;
    public $allow_employee_form_lang;
    public $identifier;
    public $submit_action;
    public $currentIndex;
    public $token;
    public $tpl_vars = [];

    public function generateForm(array $fields_form): string
    {
        return '';
    }
}

class Tools
{
    public static function isSubmit(string $submit): bool
    {
        return false;
    }
    public static function getValue(string $key, $default = false)
    {
        return $default;
    }
    public static function getAdminTokenLite(string $controller): string
    {
        return '';
    }
    public static function displayPrice($price, $currency, bool $no_utf8 = false): string
    {
        return '';
    }
}

class Configuration
{
    public static function get(string $key, $id_lang = null, $id_shop_group = null, $id_shop = null)
    {
        return null;
    }
    public static function updateValue(string $key, $value, $html = false, $id_shop_group = null, $id_shop = null): bool
    {
        return true;
    }
    public static function deleteByName(string $key): bool
    {
        return true;
    }
}

class Context
{
    public $shop;
    public $language;
    public $currency;
    public $country;
    public $controller;
    public $link;
    public $cart;
    public $customer;
    public $employee;

    public static function getContext(): self
    {
        return new self();
    }
}

class Link
{
    public function getModuleLink(string $module, string $controller = 'default', array $params = [], bool $ssl = false, int $id_lang = null, int $id_shop = null): string
    {
        return '';
    }
    public function getAdminLink(string $controller, bool $with_token = true, array $params = []): string
    {
        return '';
    }
}

class Shop
{
    public $name;
    public $id;
}

class Language
{
    public $id;
    public $iso_code;
    public $name;
}

class Currency
{
    public $id;
    public $name;
    public $iso_code;
    public $numeric_iso_code;
    public $symbol;
    public $conversion_rate;
    public $deleted;
    public $active;
}

class Country
{
    public $id;
    public $id_zone;
    public $id_currency;
    public $iso_code;
    public $name;
    public $active;
}

class Customer
{
    public $id;
    public $id_shop;
    public $id_shop_group;
    public $id_default_group;
    public $id_lang;
    public $id_risk;
    public $id_gender;
    public $firstname;
    public $lastname;
    public $email;
    public $passwd;
    public $birthday;
    public $newsletter;
    public $ip_registration_newsletter;
    public $newsletter_date_add;
    public $optin;
    public $website;
    public $company;
    public $siret;
    public $ape;
    public $outstanding_allow_amount;
    public $show_public_prices;
    public $id_shop_customer;
    public $max_payment_days;
    public $secure_key;
    public $note;
    public $active;
    public $is_guest;
    public $deleted;
    public $date_add;
    public $date_upd;
}

class Cart
{
    public $id;
    public $id_shop;
    public $id_shop_group;
    public $id_carrier;
    public $delivery_option;
    public $id_lang;
    public $id_address_delivery;
    public $id_address_invoice;
    public $id_currency;
    public $id_customer;
    public $id_guest;
    public $secure_key;
    public $recyclable;
    public $gift;
    public $gift_message;
    public $mobile_theme;
    public $allow_seperated_package;
    public $date_add;
    public $date_upd;

    public function getOrderTotal(bool $with_taxes = true, int $type = 3, array $products = null, int $id_carrier = null, bool $use_cache = true): float
    {
        return 0.0;
    }
    public function getProducts(bool $refresh = false): array
    {
        return [];
    }
}

class Order
{
    public $id;
    public $reference;
    public $id_shop;
    public $id_shop_group;
    public $id_carrier;
    public $id_lang;
    public $id_customer;
    public $id_cart;
    public $id_currency;
    public $id_address_delivery;
    public $id_address_invoice;
    public $current_state;
    public $secure_key;
    public $payment;
    public $module;
    public $recyclable;
    public $gift;
    public $gift_message;
    public $mobile_theme;
    public $total_discounts;
    public $total_discounts_tax_incl;
    public $total_discounts_tax_excl;
    public $total_paid;
    public $total_paid_tax_incl;
    public $total_paid_tax_excl;
    public $total_paid_real;
    public $total_products;
    public $total_products_wt;
    public $total_shipping;
    public $total_shipping_tax_incl;
    public $total_shipping_tax_excl;
    public $carrier_tax_rate;
    public $total_wrapping;
    public $total_wrapping_tax_incl;
    public $total_wrapping_tax_excl;
    public $invoice_number;
    public $delivery_number;
    public $invoice_date;
    public $delivery_date;
    public $valid;
    public $date_add;
    public $date_upd;

    public function getCurrentState(): int
    {
        return 0;
    }
}

class Employee
{
    public $id;
    public $id_profile;
    public $id_lang;
    public $lastname;
    public $firstname;
    public $email;
    public $passwd;
    public $active;
    public $optin;
    public $id_last_passwd_gen;
    public $last_passwd_gen;
    public $stats_date_from;
    public $stats_date_to;
    public $stats_compare_from;
    public $stats_compare_to;
    public $stats_compare_option;
    public $preselect_date_range;
    public $bo_color;
    public $bo_theme;
    public $bo_css;
    public $default_tab;
    public $bo_width;
    public $bo_menu;
    public $date_add;
    public $date_upd;
}

class Media
{
    public static function getMediaPath(string $file_path): string
    {
        return '';
    }
}

// Controller classes
class Controller
{
    public $context;
    public $smarty;

    public function getLanguages(): array
    {
        return [];
    }
}

class FrontController extends Controller
{
    public function init() {}
    public function setMedia() {}
    public function initContent() {}
}

class ModuleFrontController extends FrontController
{
    public $module;
}

// Admin Controller
class AdminController extends Controller
{
    public function getLanguages(): array
    {
        return [];
    }
}

// Database classes
class Db
{
    public static function getInstance(): self
    {
        return new self();
    }
    public function execute(string $sql, bool $use_cache = true): bool
    {
        return true;
    }
    public function executeS(string $sql, bool $array = true, bool $use_cache = true): array
    {
        return [];
    }
    public function getRow(string $sql): array
    {
        return [];
    }
    public function getValue(string $sql)
    {
        return null;
    }
    public function insert(string $table, array $data, bool $null_values = false, bool $use_cache = true, int $type = 3, bool $add_prefix = true): bool
    {
        return true;
    }
    public function update(string $table, array $data, string $where = '', int $limit = 0, bool $null_values = false, bool $use_cache = true, bool $add_prefix = true): bool
    {
        return true;
    }
    public function delete(string $table, string $where = '', int $limit = 0, bool $add_prefix = true): bool
    {
        return true;
    }
}

// Validation classes
class Validate
{
    public static function isEmail(string $email): bool
    {
        return true;
    }
    public static function isInt($value): bool
    {
        return true;
    }
    public static function isFloat($value): bool
    {
        return true;
    }
    public static function isString($string): bool
    {
        return true;
    }
    public static function isLoadedObject($object): bool
    {
        return true;
    }
}

// Smarty template engine
class Smarty
{
    public function assign($tpl_var, $value = null) {}
    public function fetch(string $template): string
    {
        return '';
    }
}

// Exceptions
class PrestaShopException extends Exception {}
class PrestaShopModuleException extends PrestaShopException {}

// Constants
define('_PS_VERSION_', '9.0.0');
define('_PS_ROOT_DIR_', '/var/www/presta9.avalonbg.com');
define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/');
define('_PS_IMG_DIR_', _PS_ROOT_DIR_ . '/img/');
define('_PS_CORE_DIR_', _PS_ROOT_DIR_ . '/classes/');
define('_PS_OVERRIDE_DIR_', _PS_ROOT_DIR_ . '/override/');
define('_PS_CACHE_DIR_', _PS_ROOT_DIR_ . '/cache/');
define('_PS_CONFIG_DIR_', _PS_ROOT_DIR_ . '/config/');
define('_PS_UPLOAD_DIR_', _PS_ROOT_DIR_ . '/upload/');
define('_PS_DOWNLOAD_DIR_', _PS_ROOT_DIR_ . '/download/');
define('_PS_MAIL_DIR_', _PS_ROOT_DIR_ . '/mails/');
define('_PS_THEME_DIR_', _PS_ROOT_DIR_ . '/themes/');
define('_PS_JS_DIR_', _PS_ROOT_DIR_ . '/js/');
define('_PS_CSS_DIR_', _PS_ROOT_DIR_ . '/css/');

// Common configuration keys
define('PS_OS_PAYMENT', 2);
define('PS_OS_PREPARATION', 3);
define('PS_OS_SHIPPING', 4);
define('PS_OS_DELIVERED', 5);
define('PS_OS_CANCELED', 6);
define('PS_OS_REFUND', 7);
define('PS_OS_ERROR', 8);
define('PS_OS_OUTOFSTOCK', 9);
define('PS_OS_BANKWIRE', 10);
define('PS_OS_PAYPAL', 10);
define('PS_OS_WS_PAYMENT', 11);
