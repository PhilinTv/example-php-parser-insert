<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/3/18
 * Time: 11:51 PM
 */

namespace Sokyrko\DependencyInjector\Rule;

use Sokyrko\DependencyInjector\Config\InjectorConfig;

interface RuleInterface
{
    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array applying errors
     */
    public function apply(InjectorConfig $injectorConfig): array;

    /**
     * @return string
     */
    public function getName(): string;
}
