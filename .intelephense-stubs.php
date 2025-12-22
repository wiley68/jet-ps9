<?php

/**
 * PrestaShop Core Classes Stubs for Intelephense
 * These stubs help avoid false error messages in the editor
 */

namespace Symfony\Component\HttpFoundation {

    class Request
    {
        public $request;

        public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [])
        {
            $this->request = (object)$request;
        }

        public function getMethod(): string
        {
            return 'GET';
        }

        public function getBasePath(): string
        {
            return '';
        }
        public function get(string $key, $default = null)
        {
            return $default;
        }
        public function isMethod(string $method): bool
        {
            return false;
        }
    }

    class Response
    {
        public function __construct(string $content = '', int $status = 200, array $headers = []) {}
        public function setContent(string $content): self
        {
            return $this;
        }
        public function getContent(): string
        {
            return '';
        }
        public function send(): self
        {
            return $this;
        }
    }

    class JsonResponse
    {
        public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false) {}
    }
}

// Symfony Framework Bundle
namespace Symfony\Bundle\FrameworkBundle\Controller {

    abstract class AbstractController
    {
        public function render(string $template, array $params = []): \Symfony\Component\HttpFoundation\Response
        {
            return new \Symfony\Component\HttpFoundation\Response('', 200);
        }

        public function renderView(string $template, array $params = []): string
        {
            return '';
        }

        public function generateUrl(string $route, array $params = []): string
        {
            return '';
        }

        public function redirectToRoute(string $route, array $params = []): \Symfony\Component\HttpFoundation\Response
        {
            return new \Symfony\Component\HttpFoundation\Response('', 302, ['Location' => '']);
        }

        public function get(string $service)
        {
            return null;
        }

        public function addFlash(string $type, string $message): void {}

        public function createForm(string $type, $data = null, array $options = []): mixed
        {
            return null;
        }
    }
}

// Symfony Form classes
namespace Symfony\Component\Form {

    abstract class AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void {}
        public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver): void {}
        public function getBlockPrefix(): string
        {
            return '';
        }
    }

    interface FormBuilderInterface
    {
        public function add(string $name, string $type = null, array $options = []): self;
        public function getForm(): FormInterface;
    }

    interface FormInterface
    {
        public function handleRequest(\Symfony\Component\HttpFoundation\Request $request): void;
        public function isSubmitted(): bool;
        public function isValid(): bool;
        public function getData(): mixed;
    }
}

// Symfony Form Extension Core Types
namespace Symfony\Component\Form\Extension\Core\Type {

    class TextType extends \Symfony\Component\Form\AbstractType
    {
        public function getParent(): string
        {
            return 'form';
        }
    }

    class NumberType extends \Symfony\Component\Form\AbstractType
    {
        public function getParent(): string
        {
            return 'text';
        }
    }

    class ChoiceType extends \Symfony\Component\Form\AbstractType
    {
        public function getParent(): string
        {
            return 'form';
        }
    }
}

// Symfony Options Resolver
namespace Symfony\Component\OptionsResolver {

    class OptionsResolver
    {
        public function setDefaults(array $defaults): self
        {
            return $this;
        }
        public function setRequired(array $required): self
        {
            return $this;
        }
        public function setAllowedTypes(string $option, array $types): self
        {
            return $this;
        }
    }
}

namespace {

    // Core classes

    // Global namespace classes
    class Cart extends ObjectModel
    {
        public const BOTH = 1;

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

        public function add($auto_date = true, $null_values = false): bool
        {
            return true;
        }

        public function updateQty(int $quantity, int $id_product, int $id_product_attribute = null, int $id_customization = null, string $operator = 'up'): bool
        {
            return true;
        }

        public function getOrderTotal(bool $with_taxes = true, int $type = 3, array $products = null, int $id_carrier = null, bool $use_cache = true): float
        {
            return 0.0;
        }

        public function getProducts(bool $refresh = false): array
        {
            return [];
        }
    }

    class Category extends ObjectModel
    {
        public $id;
        public $name;
        public $active;
    }

    class Mail
    {
        public static function Send(int $id_lang, string $template, string $subject, array $template_vars, string $to, string $to_name = null, string $from = null, string $from_name = null, array $file_attachment = null, array $smtp_options = null, string $dir_mail = null, bool $die = true, int $id_shop = null, array $bcc = null, string $reply_to = null, string $reply_to_name = null): bool
        {
            return true;
        }
    }

    class Order extends ObjectModel
    {
        public static function getByCartId(int $id_cart): ?Order
        {
            return null;
        }
    }

    class Currency extends ObjectModel
    {
        public $id;
        public $name;
        public $iso_code;
        public $conversion_rate;

        public static function getIdByIsoCode(string $iso_code): ?int
        {
            return null;
        }
    }

    class Language extends ObjectModel
    {
        public $id;
        public $name;
        public $iso_code;
        public $active;
    }

    class Country extends ObjectModel
    {
        public $id;
        public $name;
        public $iso_code;
        public $active;
    }

    // PrestaShop Form Types
}

// PrestaShop Core Configuration
namespace PrestaShop\PrestaShop\Core\Configuration {

    interface DataConfigurationInterface
    {
        public function getConfiguration(): array;
        public function updateConfiguration(array $configuration): array;
        public function validateConfiguration(array $configuration): bool;
    }
}

namespace PrestaShop\PrestaShop\Core {

    interface ConfigurationInterface
    {
        public function get(string $key, $default = null);
        public function set(string $key, $value): bool;
    }
}

// PrestaShop Core Form
namespace PrestaShop\PrestaShop\Core\Form {

    interface FormDataProviderInterface
    {
        public function getData(): array;
        public function setData(array $data): array;
    }

    class Handler
    {
        public function __construct($formFactory, $hookDispatcher, $formDataProvider, string $formType, string $formName) {}

        public function getForm(): mixed
        {
            return null;
        }

        public function save(array $data): array
        {
            return [];
        }

        public function fetch(string $template): string
        {
            return '';
        }
    }
}

// Symfony Dependency Injection
namespace Symfony\Component\DependencyInjection {

    interface ContainerAwareInterface
    {
        public function setContainer(ContainerInterface $container = null);
    }

    interface ContainerInterface
    {
        public function get(string $id);
        public function has(string $id): bool;
        public function set(string $id, $service);
    }
}

// PrestaShop Adapters
namespace PrestaShop\PrestaShop\Adapter {

    class SymfonyContainer
    {
        public static function getInstance()
        {
            return new self();
        }

        public function get(string $service)
        {
            // Return a mock router object
            return new class {
                public function generate(string $route, array $params = []): string
                {
                    return '';
                }
            };
        }
    }
}

namespace PrestaShopBundle\Form\Admin\Type {

    class TranslatorAwareType extends \Symfony\Component\Form\AbstractType
    {
        public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options): void {}
        public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver): void {}
        public function trans(string $id, string $domain, array $parameters = []): string
        {
            return $id;
        }
    }

    class SwitchType extends \Symfony\Component\Form\AbstractType
    {
        public function getParent(): string
        {
            return 'Symfony\Component\Form\Extension\Core\Type\CheckboxType';
        }
    }
}

// PrestaShop Bundle Admin Controller
namespace PrestaShopBundle\Controller\Admin {

    class PrestaShopAdminController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
    {
        public function generateUrl(string $route, array $params = []): string
        {
            return '';
        }

        public function redirectToRoute(string $route, array $params = []): \Symfony\Component\HttpFoundation\Response
        {
            return new \Symfony\Component\HttpFoundation\Response('', 302, ['Location' => '']);
        }

        public function addFlash(string $type, string $message): void {}

        public function render(string $template, array $params = []): \Symfony\Component\HttpFoundation\Response
        {
            return new \Symfony\Component\HttpFoundation\Response('', 200);
        }

        public function renderWithTemplate(string $template, array $params = []): \Symfony\Component\HttpFoundation\Response
        {
            return new \Symfony\Component\HttpFoundation\Response('', 200);
        }

        public function renderView(string $template, array $params = []): string
        {
            return '';
        }

        public function flashErrors(array $errors): void {}

        public function trans(string $id, array $parameters = [], string $domain = null): string
        {
            return $id;
        }

        public function addJs(string $path): void {}

        public function get(string $service)
        {
            // Return a mock object for dependency injection services
            return new class {
                public function getForm()
                {
                    return $this;
                }
                public function handleRequest($request)
                {
                    return $this;
                }
                public function isSubmitted()
                {
                    return false;
                }
                public function isValid()
                {
                    return true;
                }
                public function getData()
                {
                    return [];
                }
                public function createView()
                {
                    return $this;
                }
                public function save($data = null)
                {
                    return [];
                }
            };
        }
    }
}

namespace {

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
        public function executeS($sql, bool $array = true, bool $use_cache = true): array
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
        public function delete(string $table, string $where = '', int $limit = 0, bool $use_cache = true, bool $use_null = false): bool
        {
            return true;
        }
        public function insert(string $table, array $data, bool $null_values = false, bool $use_cache = true, int $type = 3, bool $add_prefix = true): bool
        {
            return true;
        }
        public function update(string $table, array $data, string $where = '', int $limit = 0, bool $null_values = false, bool $use_cache = true, bool $add_prefix = true): bool
        {
            return true;
        }
    }

    class DbQuery
    {
        public function __construct() {}
        public function from(string $table, string $alias = null): self
        {
            return $this;
        }
        public function select(string $fields): self
        {
            return $this;
        }
        public function where(string $condition): self
        {
            return $this;
        }
        public function orderBy(string $order): self
        {
            return $this;
        }
        public function limit(int $limit, int $offset = 0): self
        {
            return $this;
        }
        public function build(): string
        {
            return '';
        }
        public function delete(string $table = null): self
        {
            return $this;
        }
        public function __toString(): string
        {
            return $this->build();
        }
    }

    class Request
    {
        public function getMethod(): string
        {
            return 'GET';
        }
        public function get(string $key, $default = null)
        {
            return $default;
        }
        public function isMethod(string $method): bool
        {
            return false;
        }
    }

    class Response
    {
        public function __construct(string $content = '', int $status = 200, array $headers = []) {}
        public function setContent(string $content): self
        {
            return $this;
        }
        public function getContent(): string
        {
            return '';
        }
        public function send(): self
        {
            return $this;
        }
    }

    // Symfony Container
    class SymfonyContainer
    {
        public static function getInstance()
        {
            return new self();
        }

        public function get(string $service)
        {
            // Return a mock router object
            return new class {
                public function generate(string $route, array $params = []): string
                {
                    return '';
                }
            };
        }
    }

    class Product extends ObjectModel
    {
        public $id;
        public $name;
        public $price;
        public $active;
        public $id_category_default;

        public static function getPriceStatic(int $id_product, bool $tax = true, int $id_product_attribute = null, int $decimals = 6): float
        {
            return 0.0;
        }
    }

    class ObjectModel
    {
        public const TYPE_INT = 1;
        public const TYPE_BOOL = 2;
        public const TYPE_STRING = 3;
        public const TYPE_FLOAT = 4;
        public const TYPE_DATE = 5;
        public const TYPE_HTML = 6;
        public const TYPE_NOTHING = 7;
        public const TYPE_SQL = 8;

        public static $definition = [];
        public $id;

        public function __construct($id = null, $id_lang = null, $id_shop = null) {}

        public function save(): bool
        {
            return true;
        }

        public function delete(): bool
        {
            return true;
        }

        public static function getFieldsValidate(): array
        {
            return [];
        }

        public static function getValidationRules(): array
        {
            return [];
        }

        public function validateFields($die = true, $error_return = false): bool
        {
            return true;
        }

        public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = false)
        {
            return true;
        }

        public function add($auto_date = true, $null_values = false): bool
        {
            return true;
        }

        public function update($null_values = false): bool
        {
            return true;
        }

        public static function hydrateCollection($class, array $data, $id_lang = null): array
        {
            return [];
        }

        public function hydrate(array $data, $id_lang = null): self
        {
            return $this;
        }
    }

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
        public $active;
        public $context;
        public $smarty;

        public static function getInstanceByName(string $module_name)
        {
            return null;
        }

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
        public function trans(string $id, string $domain, array $parameters = []): string
        {
            return $id;
        }
        public function get(string $service)
        {
            if ($service === 'router') {
                return new \PrestaShop\PrestaShop\Adapter\Router();
            }
            return null;
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
        public $is_eu_compatible;
        public $currencies;
        public $currencies_mode;

        public function checkCurrency($currency): bool
        {
            return true;
        }

        public function fetch(string $template): string
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
        public static function redirectAdmin(string $url): void {}
        public static function getToken(bool $static = true): string
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
}

namespace PrestaShop\PrestaShop\Core\Payment {

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
        public function setModuleName(string $moduleName): self
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
}

namespace PrestaShop\PrestaShop\Adapter {

    class Router
    {
        public function generate(string $route, array $params = []): string
        {
            return '';
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
        public function getBaseLink(int $id_shop = null, string $ssl = null): string
        {
            return '';
        }
    }

    class Shop
    {
        public $name;
        public $id;
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

    class ModuleAdminController extends AdminController
    {
        public $ajax = false;
        public $module;

        public function __construct() {}

        public function initContent(): void {}

        public function ajaxProcess(): void {}

        public function ajaxDie($value): void
        {
            die($value);
        }
    }

    class ModuleFrontController extends FrontController
    {
        public $module;

        public function isXmlHttpRequest(): bool
        {
            return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
        }
    }

    // Admin Controller
    class AdminController extends Controller
    {
        public function getLanguages(): array
        {
            return [];
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
    class PrestaShopExceptionCore extends \Exception {}
    class PrestaShopException extends PrestaShopExceptionCore {}
    class PrestaShopModuleExceptionCore extends PrestaShopException {}
    class PrestaShopModuleException extends PrestaShopModuleExceptionCore {}

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
    define('_DB_PREFIX_', 'ps_');

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

    // Global functions
    function pSQL($string, $htmlOK = false): string
    {
        return (string)$string;
    }
}
