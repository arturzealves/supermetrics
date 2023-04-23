<?php

namespace FictionalAuth\Service;

/**
 * Class FictionalAuthService
 *
 * A very simple auth service stub implementation
 *
 * @package FictionalAuth\Service
 */
class FictionalAuthService
{
    /**
     * @return array
     */
    public function getCurrentUser(): array
    {
        return [
            'email' => 'your@email.address',
            'name'  => 'YourName',
        ];
    }
}
