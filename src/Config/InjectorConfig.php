<?php

namespace Sokyrko\DependencyInjector\Config;

class InjectorConfig
{
    /**
     * @var string example: \App\Test\TestDependencyProvider::provide
     */
    protected $methodReference;

    /**
     * @var string class name
     */
    protected $insertInstanceOf;

    /**
     * @var string example: \App\Test\TestDependency
     */
    protected $beforeClassName;

    /**
     * @var string example: \App\Test\TestDependency
     */
    protected $afterClassName;

    /**
     * @var string
     */
    protected $rule;

    /**
     * @return string
     */
    public function getMethodReference(): string
    {
        return $this->methodReference;
    }

    /**
     * @param string $methodReference
     */
    public function setMethodReference(string $methodReference): void
    {
        $this->methodReference = $methodReference;
    }

    /**
     * @return string
     */
    public function getInsertInstanceOf(): string
    {
        return $this->insertInstanceOf;
    }

    /**
     * @param string $insertInstanceOf
     */
    public function setInsertInstanceOf(string $insertInstanceOf): void
    {
        $this->insertInstanceOf = $insertInstanceOf;
    }

    /**
     * @return string
     */
    public function getBeforeClassName(): ?string
    {
        return $this->beforeClassName;
    }

    /**
     * @param string $beforeClassName
     */
    public function setBeforeClassName(string $beforeClassName): void
    {
        $this->beforeClassName = $beforeClassName;
    }

    /**
     * @return string
     */
    public function getAfterClassName(): ?string
    {
        return $this->afterClassName;
    }

    /**
     * @param string $afterClassName
     */
    public function setAfterClassName(string $afterClassName): void
    {
        $this->afterClassName = $afterClassName;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @param string $rule
     */
    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }
}
