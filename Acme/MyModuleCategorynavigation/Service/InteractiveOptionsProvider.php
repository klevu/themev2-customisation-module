<?php

namespace Acme\MyModuleCategorynavigation\Service;

use Klevu\FrontendJs\Api\InteractiveOptionsProviderInterface;
use Klevu\FrontendJs\Api\IsEnabledConditionInterface;

class InteractiveOptionsProvider implements InteractiveOptionsProviderInterface
{
    /**
     * @var IsEnabledConditionInterface
     */
    private $isEnabledCondition;

    /**
     * @param IsEnabledConditionInterface $isEnabledCondition
     */
    public function __construct(
        IsEnabledConditionInterface $isEnabledCondition
    ) {
        $this->isEnabledCondition = $isEnabledCondition;
    }

    /**
     * @param int|null $storeId
     * @return array[]
     */
    public function execute($storeId = null)
    {
        // Ensure the configuration overrides should be sent, based on the IsEnabledCondition
        //  defined in di.xml
        if (!$this->isEnabledCondition->execute($storeId)) {
            return [];
        }

        return [
            'powerUp' => [
                // Defer smart category merchandising power up
                'catnav' => false,
            ],
        ];
    }
}
