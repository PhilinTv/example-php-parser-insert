<?php

namespace Sokyrko\DependencyInjectorTest\TestProvider;

use Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\One;
use Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\Three;
use Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\Two;

class DependencyProvider
{
    /**
     * @return array
     */
    public function provide(): array
    {
        return [
            new One(),
            new Two(),
            new Three(),
        ];
    }
}
