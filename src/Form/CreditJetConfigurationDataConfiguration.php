<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * CreditJet Configuration Data Handler
 *
 * This class handles the persistence and retrieval of CreditJet module configuration settings.
 * It manages all configuration keys used by the module, including credit settings, email configurations,
 * interest rates, display options, and currency settings through PrestaShop's configuration system.
 *
 * @implements DataConfigurationInterface
 */
final class CreditJetConfigurationDataConfiguration implements DataConfigurationInterface
{
    // Configuration keys for CreditJet module settings

    /**
     * Module enable/disable status
     */
    public const JET_STATUS_IN = 'JET_STATUS_IN';

    /**
     * Email address for sending credit requests
     */
    public const JET_EMAIL = 'JET_EMAIL';

    /**
     * Store identifier for credit requests
     */
    public const JET_ID = 'JET_ID';

    /**
     * Interest rate percentage for regular payments
     */
    public const JET_PURCENT = 'JET_PURCENT';

    /**
     * Default number of installments
     */
    public const JET_VNOSKI_DEFAULT = 'JET_VNOSKI_DEFAULT';

    /**
     * Whether to show credit button in cart
     */
    public const JET_CART_SHOW = 'JET_CART_SHOW';

    /**
     * Whether to enable card payment option
     */
    public const JET_CARD_IN = 'JET_CARD_IN';

    /**
     * Interest rate percentage for card payments
     */
    public const JET_PURCENT_CARD = 'JET_PURCENT_CARD';

    /**
     * Current credit order number counter
     */
    public const JET_COUNT = 'JET_COUNT';

    /**
     * Spacing above credit button (in pixels)
     */
    public const JET_GAP = 'JET_GAP';

    /**
     * Block title text above credit button
     */
    public const JET_Z1 = 'JET_Z1';

    /**
     * Whether to show installment information
     */
    public const JET_VNOSKA = 'JET_VNOSKA';

    /**
     * Minimum price for credit purchases
     */
    public const JET_MINPRICE = 'JET_MINPRICE';

    /**
     * Currency operation mode (leva/euro display and conversion)
     */
    public const JET_EUR = 'JET_EUR';

    /**
     * PrestaShop configuration service
     *
     * Used to read and write configuration values to the PrestaShop configuration table.
     *
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * Constructor
     *
     * @param ConfigurationInterface $configuration PrestaShop configuration service
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get all CreditJet configuration values
     *
     * Retrieves all configuration settings for the CreditJet module from the database
     * and returns them as an associative array with lowercase keys for form compatibility.
     *
     * @return array Configuration values array with keys like 'jet_status_in', 'jet_email', etc.
     */
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

    /**
     * Update CreditJet configuration values
     *
     * Validates and saves the provided configuration data to the database.
     * If validation passes, all configuration keys are updated. Special handling
     * is applied for the JET_COUNT field (defaults to 1 if empty).
     *
     * @param array $configuration Configuration data to save
     *
     * @return array Array of validation errors (empty if successful)
     */
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
     * Validate configuration data before saving
     *
     * Performs basic validation on the configuration array to ensure required fields are present.
     * Currently only checks for the presence of 'jet_status_in' field.
     *
     * @param array $configuration Configuration data to validate
     *
     * @return bool Returns true if validation passes, false otherwise
     */
    public function validateConfiguration(array $configuration): bool
    {
        return
            isset($configuration['jet_status_in']);
    }
}
