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

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

/**
 * CreditJet Payment Module for PrestaShop 9.x
 * PB Personal Finance Credit Calculator
 */
class CreditJet extends PaymentModule
{
    const HOOKS = [
        'ActionFrontControllerSetMedia'
    ];

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

        if (!Configuration::get('CREDITJET_NAME')) {
            $this->warning = $this->l('No merchant ID provided');
        }
    }

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

    private function registerHooks(): bool
    {
        foreach (static::HOOKS as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }
        return true;
    }

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

    public function getContent(): string
    {
        $route = $this->get('router')->generate('credit_jet_configuration_form');
        Tools::redirectAdmin($route);
        return '';
    }
}
