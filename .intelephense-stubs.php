<?php

/**
 * Символи за Intelephense — не се include-ва от PrestaShop при runtime.
 * Ядрото на PS дефинира тези класове; тук са само за автодопълване и без грешки „Undefined type“.
 */

namespace PrestaShop\PrestaShop\Core\Payment {

    class PaymentOption
    {
        public function setModuleName(string $moduleName): self
        {
            return $this;
        }

        public function setCallToActionText(string $text): self
        {
            return $this;
        }

        public function setAdditionalInformation(string $info): self
        {
            return $this;
        }
    }
}

namespace PrestaShop\PrestaShop\Core\Form {

    interface FormDataProviderInterface
    {
        /**
         * @return array<string, mixed>
         */
        public function getData(): array;

        /**
         * @param array<string, mixed> $data
         *
         * @return array<string, mixed>
         */
        public function setData(array $data): array;
    }

    class Handler
    {
        /**
         * @return mixed
         */
        public function getForm()
        {
            return new class() {
                public function handleRequest($request): void {}

                public function isSubmitted(): bool
                {
                    return false;
                }

                public function isValid(): bool
                {
                    return false;
                }

                public function getData(): array
                {
                    return [];
                }

                public function createView()
                {
                    return null;
                }
            };
        }

        /**
         * @param array<string, mixed> $data
         *
         * @return array<int|string, mixed>
         */
        public function save(array $data): array
        {
            return [];
        }
    }
}

namespace PrestaShop\PrestaShop\Core\Configuration {

    interface DataConfigurationInterface
    {
        /**
         * @return array<string, mixed>
         */
        public function getConfiguration(): array;

        /**
         * @param array<string, mixed> $configuration
         *
         * @return array<int|string, mixed>
         */
        public function updateConfiguration(array $configuration): array;

        /**
         * @param array<string, mixed> $configuration
         */
        public function validateConfiguration(array $configuration): bool;
    }
}

namespace PrestaShop\PrestaShop\Core {

    interface ConfigurationInterface
    {
        /**
         * @param mixed $default
         *
         * @return mixed
         */
        public function get(string $key, $default = null);

        /**
         * @param mixed $value
         */
        public function set(string $key, $value): bool;
    }
}

namespace PrestaShop\PrestaShop\Adapter {

    class SymfonyContainer
    {
        public static function getInstance(): self
        {
            return new self();
        }

        public function get(string $id): object
        {
            return new class() {
                public function generate(string $route, array $params = []): string
                {
                    return '';
                }
            };
        }
    }
}

namespace PrestaShopBundle\Controller\Admin {

    use Symfony\Component\HttpFoundation\Response;

    class PrestaShopAdminController
    {
        /**
         * @param array<string, mixed> $parameters
         */
        public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
        {
            return $id;
        }

        public function addFlash(string $type, $message): void {}

        /**
         * @param array<int|string, mixed> $errors
         */
        public function flashErrors(array $errors): void {}

        /**
         * @param array<string, mixed> $parameters
         */
        public function redirectToRoute(string $route, array $parameters = [], int $status = 302): Response
        {
            return new Response();
        }

        /**
         * @param array<string, mixed> $parameters
         */
        public function generateUrl(string $route, array $parameters = []): string
        {
            return '';
        }

        /**
         * @param array<string, mixed> $parameters
         */
        public function render(string $view, array $parameters = [], ?Response $response = null): Response
        {
            return $response ?? new Response();
        }
    }
}

namespace PrestaShopBundle\Form\Admin\Type {

    class TranslatorAwareType
    {
        public function trans(string $id, string $domain = null, array $parameters = [], string $locale = null): string
        {
            return $id;
        }
    }

    class SwitchType {}
}

namespace Symfony\Component\Form\Extension\Core\Type {

    class ChoiceType {}

    class NumberType {}

    class TextType {}
}

namespace Symfony\Component\Form {

    interface FormBuilderInterface
    {
        /**
         * @param string $child
         * @param string|null $type
         * @param array<string, mixed> $options
         */
        public function add(string $child, $type = null, array $options = []);
    }
}

namespace Symfony\Component\HttpFoundation {

    class ParameterBag
    {
        public function get(string $key, $default = null)
        {
            return $default;
        }
    }

    class Request
    {
        /** @var ParameterBag */
        public $request;

        public function __construct()
        {
            $this->request = new ParameterBag();
        }

        public function isMethod(string $method): bool
        {
            return false;
        }
    }

    class Response
    {
        /**
         * @param int $status
         * @param array<string, string> $headers
         */
        public function __construct(string $content = '', int $status = 200, array $headers = []) {}
    }
}

namespace {

    if (!defined('_DB_PREFIX_')) {
        define('_DB_PREFIX_', 'ps_');
    }
    if (!defined('_PS_MODULE_DIR_')) {
        define('_PS_MODULE_DIR_', '');
    }

    class ObjectModelCore
    {
        public const TYPE_STRING = 3;
        public const TYPE_FLOAT = 4;
        public const TYPE_DATE = 5;
    }

    class ObjectModel extends ObjectModelCore {}

    class Validate
    {
        /**
         * @param object|false|null $object
         */
        public static function isLoadedObject($object): bool
        {
            return false;
        }

        public static function isUnsignedInt($value): bool
        {
            return false;
        }
    }

    class Module
    {
        /** @var int Module primary key (ps_module.id) */
        public $id;

        public $name;
        public $tab;
        public $version;
        public $author;
        public $ps_versions_compliancy;
        public $bootstrap;
        public $displayName;
        public $description;
        public $confirmUninstall;
        public $warning;
        public $active;
        public $context;
        public $smarty;
        public $is_eu_compatible;
        public $currencies;
        public $currencies_mode;

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

        public function l(string $string, $class = null, bool $addslashes = false, bool $htmlentities = true): string
        {
            return '';
        }

        public function fetch(string $template): string
        {
            return '';
        }

        /**
         * @return PaymentModule|null
         */
        public static function getInstanceByName(string $module_name)
        {
            return new class extends PaymentModule {
                public $displayName = '';

                public $displayName_card = '';
            };
        }

        /**
         * @return array<int, array<string, mixed>>
         */
        public static function getPaymentModules()
        {
            return [];
        }
    }

    abstract class PaymentModule extends Module
    {
        /** @var string */
        public $displayName_card;

        /** @var int ID of the order created by the last validateOrder() call */
        public $currentOrder;

        public function validateOrder(
            $id_cart,
            $id_order_state,
            $amountPaid,
            $paymentMethod = 'Unknown',
            $message = null,
            $extra_vars = [],
            $currency_special = null,
            $dont_touch_amount = false,
            $secure_key = false,
            $shop = null
        ) {}
    }

    class Controller
    {
        /** @var string */
        public $php_self;

        public function registerJavascript(string $id, string $path, array $params = []): void {}

        public function registerStylesheet(string $id, string $path, array $params = []): void {}
    }

    class FrontController extends Controller
    {
        /** @var Context */
        public $context;

        public function init() {}

        public function initContent() {}
    }

    class ModuleFrontController extends FrontController
    {
        /** @var Module */
        public $module;

        public function isXmlHttpRequest(): bool
        {
            return false;
        }
    }

    class Smarty
    {
        public function assign($tpl_var, $value = null, $merge = false): void {}
    }

    class Link
    {
        public function getBaseLink(): string
        {
            return '';
        }

        public function getModuleLink(string $module, string $controller = 'default', array $params = [], $ssl = null, $id_lang = null, $id_shop = null): string
        {
            return '';
        }

        /**
         * @param string $controller
         * @param bool|null $ssl
         * @param int|null $idLang
         * @param array|string|null $request
         */
        public function getPageLink($controller, $ssl = null, $idLang = null, $request = null, $requestUrlEncode = false, $idShop = null, $relativeProtocol = false): string
        {
            return '';
        }
    }

    class Language
    {
        /** @var int */
        public $id;

        public function __construct($id = null, $id_lang = null) {}
    }

    class Cart
    {
        public const BOTH = 3;

        /** @var int */
        public $id = 0;

        public $id_customer;
        public $id_address_delivery;
        public $id_address_invoice;
        public $id_currency;
        public $id_carrier;
        public $id_lang;
        public $recyclable;
        public $gift;

        public function __construct($id = null) {}

        public function add(): bool
        {
            return true;
        }

        public function updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up'): bool
        {
            return true;
        }

        /**
         * @param mixed $type
         */
        public function getOrderTotal($useTax = true, $type = null, $products = null, $id_carrier = null, $use_cache = true, bool $keepOrderPrices = false): float
        {
            return 0.0;
        }

        public function delete(): bool
        {
            return true;
        }

        public function isVirtualCart(): bool
        {
            return false;
        }
    }

    class Cookie
    {
        /** @var int */
        public $id_cart;
    }

    class Country
    {
        /** @var int */
        public $id;

        public function __construct($id_country = null, $id_lang = null) {}
    }

    class Order
    {
        /**
         * @param int $id_cart
         *
         * @return false|Order
         */
        public static function getByCartId($id_cart)
        {
            return new self();
        }
    }

    class Mail
    {
        /**
         * PrestaShop Mail::Send parameter order (incl. $dir_mail).
         *
         * @param mixed $id_lang
         */
        public static function Send(
            $id_lang,
            $template,
            $subject,
            $template_vars,
            $to,
            $to_name = null,
            $from = null,
            $from_name = null,
            $file_attachment = null,
            $mode_smtp = null,
            $dir_mail = null,
            $die = false,
            $id_shop = null,
            $bcc = null,
            $reply_to = null,
            $reply_to_name = null
        ) {
            return true;
        }
    }

    class Currency
    {
        /** @var int */
        public $id;

        /** @var string */
        public $iso_code;

        public function __construct($id = null, $id_lang = null, $id_shop = null) {}

        /**
         * @return false|int
         */
        public static function getIdByIsoCode(string $iso_code, int $id_shop = 0)
        {
            return false;
        }
    }

    class Customer
    {
        /** @var int */
        public $id;

        /** @var string */
        public $secure_key;

        public string $firstname;
        public string $lastname;
        public string $email;

        public function __construct($id = null) {}

        public function isLogged(): bool
        {
            return false;
        }

        /** @return array<int, array{phone: string, id_address: int}> */
        public function getAddresses(int $id_lang): array
        {
            return [];
        }
    }

    class Context
    {
        /** @var Controller */
        public $controller;

        /** @var Currency|null */
        public $currency;

        /** @var Customer */
        public $customer;

        /** @var Link */
        public $link;

        /** @var Language */
        public $language;

        /** @var Smarty */
        public $smarty;

        /** @var Cart|null */
        public $cart;

        /** @var Country|null */
        public $country;

        /** @var Cookie */
        public $cookie;
    }

    class Category
    {
        /** @var string|array<int|string, string> */
        public $name;

        public function __construct($id_category = null, $id_lang = null) {}
    }

    class Product
    {
        /** @var int */
        public $id;

        /** @var int */
        public $id_category_default;

        /** @var string|array<int|string, string> */
        public $name;

        public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, ?Context $context = null) {}

        public static function getPriceStatic(
            int $id_product,
            bool $usetax = true,
            $id_product_attribute = null,
            $decimals = 6,
            $divisor = null,
            $only_reduc = false,
            $usereduc = true,
            $quantity = 1,
            $force_associated_tax = false,
            $id_customer = null,
            $id_cart = null,
            $id_address = null,
            &$specific_price_output = null,
            $with_ecotax = true,
            $use_group_reduction = true,
            ?Context $context = null,
            $use_customer_price = true,
            $id_customization = null
        ): float {
            return 0.0;
        }
    }

    class Configuration
    {
        public static function get(string $key, $id_lang = null, $id_shop_group = null, $id_shop = null)
        {
            return null;
        }

        public static function updateValue(string $key, $value, bool $html = false, $id_shop_group = null, $id_shop = null): bool
        {
            return true;
        }

        public static function deleteByName(string $key): bool
        {
            return true;
        }
    }

    class Tools
    {
        /**
         * @param string $url
         * @param string $base_uri
         * @param Link|null $link
         * @param mixed|null $headers
         */
        public static function redirect($url, $base_uri = '', $link = null, $headers = null): void {}

        public static function redirectAdmin(string $url): void {}

        public static function getToken(bool $static = true): string
        {
            return '';
        }

        public static function isSubmit(string $submit): bool
        {
            return false;
        }

        public static function getValue(string $key, $default_value = false)
        {
            return $default_value;
        }
    }

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

        /**
         * @param array<string, mixed> $data
         */
        public function insert(string $table, array $data, bool $null_values = false, bool $use_cache = true, int $type = 3, bool $add_prefix = true): bool
        {
            return true;
        }

        public function delete(string $table, string $where = '', int $limit = 0, bool $use_cache = true, bool $use_null = false): bool
        {
            return true;
        }

        /**
         * @param string|DbQuery $sql
         *
         * @return mixed
         */
        public function query($sql)
        {
            return false;
        }

        /**
         * @param string|DbQuery $sql
         *
         * @return mixed
         */
        public function getValue($sql, bool $use_cache = true)
        {
            return null;
        }

        /**
         * @return array<int, array<string, mixed>>
         */
        public function executeS($sql, bool $array = true, bool $use_cache = true): array
        {
            return [];
        }
    }

    class DbQuery
    {
        public function select(string $fields): self
        {
            return $this;
        }

        public function from(string $table, ?string $alias = null): self
        {
            return $this;
        }

        public function where(string $condition): self
        {
            return $this;
        }

        public function orderBy(string $fields): self
        {
            return $this;
        }

        public function build(): string
        {
            return (string) $this;
        }

        public function __toString(): string
        {
            return '';
        }
    }

    function pSQL($string, bool $htmlOK = false): string
    {
        return (string) $string;
    }

    /**
     * @param resource $result
     *
     * @return array<string, mixed>|false
     */
    function mysql_fetch_assoc($result)
    {
        return false;
    }
}
