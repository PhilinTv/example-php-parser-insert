<?php

namespace App\Test;

use App\Test\Dependencies\DependencyFour;
use App\Test\Dependencies\DependencyOne;
use App\Test\Dependencies\DependencyThree;
use App\Test\Dependencies\DependencyTwo;

class TestDependencyProvider
{
    /**
     * Test comment. Should be not overridden.
     *
     * @return array
     */
    public function provide(): array
    {
        return [
            new DependencyOne(),
            new DependencyTwo(),
            new DependencyThree(),
            new DependencyFour(),
        ];
    }
}
