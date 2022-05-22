<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestHandler
{
    private array $defaultParams = [
        'filters' => [
            'job_offer' => null,
            'category_id' => null,
            'company_id' => null,
        ],
        'orders' => [
            // 'job_offer' => 'asc',
            // 'category' => 'asc',
            // 'company' => 'asc',
        ]
    ];

    private array $requestParams = [];

    private array $configuredParams = [];

    private array $currentOrderFlags = [];

    public function __construct(RequestStack $requestStack)
    {
        $this->requestParams = array_merge($this->defaultParams, $requestStack->getCurrentRequest()->query->all());
    }

    public function generateOrderLinkData(string $key): array
    {
        $this->configuredParams = $this->requestParams;

        if (array_key_exists($key, $this->configuredParams['orders'])) {
            // Current direction
            $this->currentOrderFlags[$key] = $this->configuredParams['orders'][$key] == 'asc' ? 'asc' : 'desc';

            // Reverse next direction
            $this->configuredParams['orders'][$key] = $this->configuredParams['orders'][$key] == 'asc' ? 'desc' : 'asc';
        } else {
            // Default direction
            $this->currentOrderFlags[$key] = 'default';

            // Default sortable direction
            $this->configuredParams['orders'][$key] = 'desc';
        }

        return $this->configuredParams;
    }

    public function getOrderFlag(string $key): string
    {
        return $this->currentOrderFlags[$key];
    }
}
