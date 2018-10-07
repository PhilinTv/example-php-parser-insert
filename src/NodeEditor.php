<?php

namespace Sokyrko\DependencyInjector;

use Sokyrko\DependencyInjector\Config\InjectorConfig;
use Sokyrko\DependencyInjector\Rule\RuleInterface;

class NodeEditor
{
    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array errors
     */
    public function apply(InjectorConfig $injectorConfig): array
    {
        $nonAppliedRulesErrors = [];

        $errors = $this->applyRule($injectorConfig);

        if ($errors !== []) {
            $nonAppliedRulesErrors[$injectorConfig->getRule()] = $errors;
        }

        return $nonAppliedRulesErrors;
    }

    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array errors
     */
    protected function applyRule(InjectorConfig $injectorConfig): array
    {
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $injectorConfig->getRule()) {
                return $rule->apply($injectorConfig);
            }
        }

        return [];
    }
}
