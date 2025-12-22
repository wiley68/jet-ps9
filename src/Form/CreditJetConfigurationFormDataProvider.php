<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * CreditJet Configuration Form Data Provider
 *
 * This provider is responsible for providing form data for the CreditJet module configuration.
 * It acts as a bridge between the form system and the configuration component,
 * retrieving and updating configuration values through the DataConfigurationInterface.
 *
 * @implements FormDataProviderInterface
 */
class CreditJetConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * Data configuration service for CreditJet module
     *
     * Handles retrieval and updates of CreditJet configuration values
     * from the PrestaShop configuration system.
     *
     * @var DataConfigurationInterface
     */
    private $creditJetConfigurationDataConfiguration;

    /**
     * Constructor
     *
     * @param DataConfigurationInterface $creditJetConfigurationDataConfiguration
     *        The data configuration service for handling CreditJet settings
     */
    public function __construct(DataConfigurationInterface $creditJetConfigurationDataConfiguration)
    {
        $this->creditJetConfigurationDataConfiguration = $creditJetConfigurationDataConfiguration;
    }

    /**
     * Get configuration data for the form
     *
     * Retrieves current CreditJet configuration values to populate the form fields.
     *
     * @return array Configuration data array
     */
    public function getData(): array
    {
        return $this->creditJetConfigurationDataConfiguration->getConfiguration();
    }

    /**
     * Set configuration data from the form
     *
     * Updates CreditJet configuration values with data submitted from the form.
     *
     * @param array $data Form data to update configuration with
     *
     * @return array Updated configuration data
     */
    public function setData(array $data): array
    {
        return $this->creditJetConfigurationDataConfiguration->updateConfiguration($data);
    }
}
