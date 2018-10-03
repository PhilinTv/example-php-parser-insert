<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/3/18
 * Time: 11:51 PM
 */

namespace App\Rule;

interface RuleInterface
{
    /**
     * @param array $parameters
     * @return array applying errors
     */
    public function apply(array $parameters): array;

    /**
     * @return string
     */
    public function getName(): string;
}
