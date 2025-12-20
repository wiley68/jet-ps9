<?php

declare(strict_types=1);

namespace PrestaShop\Module\CreditJet\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provider is responsible for providing form data, in this case, it is returned from the configuration component.
 *
 * Class CreditJetConfigurationFormDataProvider
 */
class CreditJetConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $creditJetConfigurationDataConfiguration;

    public function __construct(DataConfigurationInterface $creditJetConfigurationDataConfiguration)
    {
        $this->creditJetConfigurationDataConfiguration = $creditJetConfigurationDataConfiguration;
    }

    public function getData(): array
    {
        return $this->creditJetConfigurationDataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->creditJetConfigurationDataConfiguration->updateConfiguration($data);
    }
}
