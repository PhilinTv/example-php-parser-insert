<?php

namespace Sokyrko\DependencyInjector;

use Sokyrko\DependencyInjector\Config\InjectorConfig;

interface DependencyInjectorFacadeInterface
{
    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array errors
     */
    public function inject(InjectorConfig $injectorConfig): array;
}
