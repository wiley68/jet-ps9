<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Configuration is used to save data to configuration table and retrieve from it.
 */
final class CreditJetConfigurationDataConfiguration implements DataConfigurationInterface
{
    public const JET_STATUS_IN = 'JET_STATUS_IN';
    public const JET_EMAIL = 'JET_EMAIL';
    public const JET_ID = 'JET_ID';
    public const JET_PURCENT = 'JET_PURCENT';
    public const JET_VNOSKI_DEFAULT = 'JET_VNOSKI_DEFAULT';
    public const JET_CART_SHOW = 'JET_CART_SHOW';
    public const JET_CARD_IN = 'JET_CARD_IN';
    public const JET_PURCENT_CARD = 'JET_PURCENT_CARD';
    public const JET_COUNT = 'JET_COUNT';
    public const JET_GAP = 'JET_GAP';
    public const JET_Z1 = 'JET_Z1';
    public const JET_VNOSKA = 'JET_VNOSKA';
    public const JET_MINPRICE = 'JET_MINPRICE';
    public const JET_EUR = 'JET_EUR';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        $return = [];

        $return['jet_status_in'] = $this->configuration->get(static::JET_STATUS_IN);
        $return['jet_email'] = $this->configuration->get(static::JET_EMAIL);
        $return['jet_id'] = $this->configuration->get(static::JET_ID);
        $return['jet_purcent'] = $this->configuration->get(static::JET_PURCENT);
        $return['jet_vnoski_default'] = $this->configuration->get(static::JET_VNOSKI_DEFAULT);
        $return['jet_cart_show'] = $this->configuration->get(static::JET_CART_SHOW);
        $return['jet_card_in'] = $this->configuration->get(static::JET_CARD_IN);
        $return['jet_purcent_card'] = $this->configuration->get(static::JET_PURCENT_CARD);
        $return['jet_count'] = $this->configuration->get(static::JET_COUNT);
        $return['jet_gap'] = $this->configuration->get(static::JET_GAP);
        $return['jet_z1'] = $this->configuration->get(static::JET_Z1);
        $return['jet_vnoska'] = $this->configuration->get(static::JET_VNOSKA);
        $return['jet_minprice'] = $this->configuration->get(static::JET_MINPRICE);
        $return['jet_eur'] = $this->configuration->get(static::JET_EUR);

        return $return;
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(static::JET_STATUS_IN, $configuration['jet_status_in']);
            $this->configuration->set(static::JET_EMAIL, $configuration['jet_email']);
            $this->configuration->set(static::JET_ID, $configuration['jet_id']);
            $this->configuration->set(static::JET_PURCENT, $configuration['jet_purcent']);
            $this->configuration->set(static::JET_VNOSKI_DEFAULT, $configuration['jet_vnoski_default']);
            $this->configuration->set(static::JET_CART_SHOW, $configuration['jet_cart_show']);
            $this->configuration->set(static::JET_CARD_IN, $configuration['jet_card_in']);
            $this->configuration->set(static::JET_PURCENT_CARD, $configuration['jet_purcent_card']);
            $this->configuration->set(static::JET_COUNT, $configuration['jet_count'] ? $configuration['jet_count'] : 1);
            $this->configuration->set(static::JET_GAP, $configuration['jet_gap']);
            $this->configuration->set(static::JET_Z1, $configuration['jet_z1']);
            $this->configuration->set(static::JET_VNOSKA, $configuration['jet_vnoska']);
            $this->configuration->set(static::JET_MINPRICE, $configuration['jet_minprice']);
            $this->configuration->set(static::JET_EUR, $configuration['jet_eur']);
        }

        /* Errors are returned here. */
        return $errors;
    }

    /**
     * Ensure the parameters passed are valid.
     *
     * @return bool Returns true if no exception are thrown
     */
    public function validateConfiguration(array $configuration): bool
    {
        return
            isset($configuration['jet_status_in']);
    }
}
