<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

/**
 * CreditJet Payment Module for PrestaShop 9.x
 * Piraeus Personal Finance Credit Calculator
 */
class CreditJet extends PaymentModule
{
    /**
     * Module configuration keys
     */
    const CONFIG_MERCHANT_ID = 'CREDITJET_MERCHANT_ID';
    const CONFIG_API_KEY = 'CREDITJET_API_KEY';
    const CONFIG_SANDBOX_MODE = 'CREDITJET_SANDBOX_MODE';
    const CONFIG_MIN_AMOUNT = 'CREDITJET_MIN_AMOUNT';
    const CONFIG_MAX_AMOUNT = 'CREDITJET_MAX_AMOUNT';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'creditjet';
        $this->tab = 'payments_gateways';
        $this->version = '2.0.0';
        $this->author = 'Ilko Ivanov';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CreditJet - Париба лични финанси');
        $this->description = $this->l('Кредитен калкулатор и плащане чрез Париба лични финанси');
        $this->confirmUninstall = $this->l('Сигурни ли сте, че искате да деинсталирате CreditJet модула?');

        if (!Configuration::get(static::CONFIG_MERCHANT_ID)) {
            $this->warning = $this->l('Моля конфигурирайте модула CreditJet.');
        }
    }

    /**
     * Install module
     *
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Register hooks
        if (
            !$this->registerHook('paymentOptions') ||
            !$this->registerHook('paymentReturn') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayBackOfficeHeader')
        ) {
            return false;
        }

        // Set default configuration
        Configuration::updateValue(static::CONFIG_SANDBOX_MODE, 1);
        Configuration::updateValue(static::CONFIG_MIN_AMOUNT, 100);
        Configuration::updateValue(static::CONFIG_MAX_AMOUNT, 50000);

        return true;
    }

    /**
     * Uninstall module
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        // Remove configuration
        Configuration::deleteByName(static::CONFIG_MERCHANT_ID);
        Configuration::deleteByName(static::CONFIG_API_KEY);
        Configuration::deleteByName(static::CONFIG_SANDBOX_MODE);
        Configuration::deleteByName(static::CONFIG_MIN_AMOUNT);
        Configuration::deleteByName(static::CONFIG_MAX_AMOUNT);

        return true;
    }

    /**
     * Get module configuration page content
     *
     * @return string
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitCreditJet')) {
            $output .= $this->postProcess();
        }

        $output .= $this->renderForm();

        return $output;
    }

    /**
     * Process configuration form submission
     *
     * @return string
     */
    protected function postProcess()
    {
        $errors = [];

        $merchantId = Tools::getValue(static::CONFIG_MERCHANT_ID);
        $apiKey = Tools::getValue(static::CONFIG_API_KEY);
        $sandboxMode = (int)Tools::getValue(static::CONFIG_SANDBOX_MODE);
        $minAmount = (float)Tools::getValue(static::CONFIG_MIN_AMOUNT);
        $maxAmount = (float)Tools::getValue(static::CONFIG_MAX_AMOUNT);

        if (empty($merchantId)) {
            $errors[] = $this->l('Моля въведете Merchant ID.');
        }

        if (empty($apiKey)) {
            $errors[] = $this->l('Моля въведете API Key.');
        }

        if ($minAmount <= 0) {
            $errors[] = $this->l('Минималната сума трябва да бъде по-голяма от 0.');
        }

        if ($maxAmount <= $minAmount) {
            $errors[] = $this->l('Максималната сума трябва да бъде по-голяма от минималната.');
        }

        if (empty($errors)) {
            Configuration::updateValue(static::CONFIG_MERCHANT_ID, $merchantId);
            Configuration::updateValue(static::CONFIG_API_KEY, $apiKey);
            Configuration::updateValue(static::CONFIG_SANDBOX_MODE, $sandboxMode);
            Configuration::updateValue(static::CONFIG_MIN_AMOUNT, $minAmount);
            Configuration::updateValue(static::CONFIG_MAX_AMOUNT, $maxAmount);

            $output = $this->displayConfirmation($this->l('Настройките са запазени успешно.'));
        } else {
            $output = $this->displayError(implode('<br />', $errors));
        }

        return $output;
    }

    /**
     * Render configuration form
     *
     * @return string
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCreditJet';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Get configuration form fields
     *
     * @return array
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Настройки на CreditJet'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Merchant ID'),
                        'name' => static::CONFIG_MERCHANT_ID,
                        'required' => true,
                        'desc' => $this->l('Въведете вашия CreditJet Merchant ID'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('API Key'),
                        'name' => static::CONFIG_API_KEY,
                        'required' => true,
                        'desc' => $this->l('Въведете вашия CreditJet API Key'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Тестов режим'),
                        'name' => static::CONFIG_SANDBOX_MODE,
                        'is_bool' => true,
                        'desc' => $this->l('Активирайте за тестов режим'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Включен'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Изключен'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Минимална сума'),
                        'name' => static::CONFIG_MIN_AMOUNT,
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('Минимална сума за кредитно плащане'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Максимална сума'),
                        'name' => static::CONFIG_MAX_AMOUNT,
                        'suffix' => $this->context->currency->iso_code,
                        'desc' => $this->l('Максимална сума за кредитно плащане'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Запази'),
                ],
            ],
        ];
    }

    /**
     * Get configuration form values
     *
     * @return array
     */
    protected function getConfigFormValues()
    {
        return [
            static::CONFIG_MERCHANT_ID => Configuration::get(static::CONFIG_MERCHANT_ID),
            static::CONFIG_API_KEY => Configuration::get(static::CONFIG_API_KEY),
            static::CONFIG_SANDBOX_MODE => Configuration::get(static::CONFIG_SANDBOX_MODE),
            static::CONFIG_MIN_AMOUNT => Configuration::get(static::CONFIG_MIN_AMOUNT),
            static::CONFIG_MAX_AMOUNT => Configuration::get(static::CONFIG_MAX_AMOUNT),
        ];
    }

    /**
     * Hook: paymentOptions
     * Return payment options for checkout
     *
     * @param array $params
     * @return array
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return [];
        }

        $cart = $params['cart'];
        $total = $cart->getOrderTotal();

        // Check if payment amount is within limits
        $minAmount = Configuration::get(static::CONFIG_MIN_AMOUNT);
        $maxAmount = Configuration::get(static::CONFIG_MAX_AMOUNT);

        if ($total < $minAmount || $total > $maxAmount) {
            return [];
        }

        $paymentOption = new PaymentOption();
        $paymentOption->setCallToActionText($this->l('Плати с кредит от Париба'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
            ->setAdditionalInformation($this->l('Платете на вноски с кредит от Париба лични финанси'))
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/logo.png'));

        return [$paymentOption];
    }

    /**
     * Hook: paymentReturn
     * Handle payment return
     *
     * @param array $params
     * @return string
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return '';
        }

        $order = $params['order'];
        $state = $order->getCurrentState();

        $this->smarty->assign([
            'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
            'shop_name' => $this->context->shop->name,
            'status' => 'ok',
            'id_order' => $order->id,
        ]);

        if ($order->getCurrentState() == Configuration::get('PS_OS_PAYMENT')) {
            $this->smarty->assign('status', 'ok');
        }

        return $this->display(__FILE__, 'views/templates/hook/payment_return.tpl');
    }

    /**
     * Check if currency is supported
     *
     * @param Currency $currency
     * @return bool
     */
    public function checkCurrency($currency)
    {
        return in_array($currency->iso_code, ['BGN', 'EUR']);
    }

    /**
     * Check if module is configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return Configuration::get(static::CONFIG_MERCHANT_ID) &&
            Configuration::get(static::CONFIG_API_KEY);
    }
}
