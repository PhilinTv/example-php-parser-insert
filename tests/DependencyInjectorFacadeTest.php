<?php


namespace Sokyrko\DependencyInjectorTest;

use PHPUnit\Framework\TestCase;
use Sokyrko\DependencyInjector\Config\InjectorConfig;
use Sokyrko\DependencyInjector\DependencyInjectorFacade;
use Sokyrko\DependencyInjector\Rule\InsertReturnDependencyRule;

class DependencyInjectorFacadeTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunInjectReturnDependencyRuleEnd()
    {
        $injectorConfig = new InjectorConfig();
        $injectorConfig->setInsertInstanceOf('Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\NewDep');
        $injectorConfig->setMethodReference('\Sokyrko\DependencyInjectorTest\TestProvider\DependencyProvider::provide');
        $injectorConfig->setRule(InsertReturnDependencyRule::NAME);

        $errors = (new DependencyInjectorFacade())->inject($injectorConfig);

        $this->assertEmpty($errors, print_r($errors));
    }

    /**
     * @return void
     */
    public function testRunInjectReturnDependencyRuleBefore()
    {
        $injectorConfig = new InjectorConfig();
        $injectorConfig->setInsertInstanceOf('Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\NewDep');
        $injectorConfig->setMethodReference('\Sokyrko\DependencyInjectorTest\TestProvider\DependencyProvider::provide');
        $injectorConfig->setBeforeClassName('\Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\Two');
        $injectorConfig->setRule(InsertReturnDependencyRule::NAME);

        $errors = (new DependencyInjectorFacade())->inject($injectorConfig);

        $this->assertEmpty($errors, print_r($errors));
    }

    /**
     * @return void
     */
    public function testRunInjectReturnDependencyRuleAfter()
    {
        $injectorConfig = new InjectorConfig();
        $injectorConfig->setInsertInstanceOf('Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\NewDep');
        $injectorConfig->setMethodReference('\Sokyrko\DependencyInjectorTest\TestProvider\DependencyProvider::provide');
        $injectorConfig->setAfterClassName('\Sokyrko\DependencyInjectorTest\TestProvider\Dependencies\Two');
        $injectorConfig->setRule(InsertReturnDependencyRule::NAME);

        $errors = (new DependencyInjectorFacade())->inject($injectorConfig);

        $this->assertEmpty($errors, print_r($errors));
    }
}
