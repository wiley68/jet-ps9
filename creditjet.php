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

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

/**
 * CreditJet Payment Module for PrestaShop 9.x
 * PB Personal Finance Credit Calculator
 */
class CreditJet extends PaymentModule
{
    const HOOKS = [];

    /**
     * Module configuration keys
     */
    const CONFIG_CREDITJET_NAME = 'CREDITJET_NAME';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'creditjet';
        $this->tab = 'payments_gateways';
        $this->version = '1.8.0';
        $this->author = 'Ilko Ivanov';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => '9.99.99'];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('PB Personal Finance');
        $this->description = $this->l('Allows your customers to purchase goods on installments with PB Personal Finance');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        if (!Configuration::get(static::CONFIG_CREDITJET_NAME)) {
            $this->warning = $this->l('No merchant ID provided');
        }
    }
}
