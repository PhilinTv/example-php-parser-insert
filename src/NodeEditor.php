<?php

namespace App;

use App\Rule\RuleInterface;

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
     * @param array $config
     *
     * @return array errors
     */
    public function apply(array $config): array
    {
        $nonAppliedRulesErrors = [];

        foreach ($config['rules'] as $ruleName => $parameters) {
            $errors = $this->applyRule($ruleName, $parameters);

            if ($errors !== []) {
                $nonAppliedRulesErrors[$ruleName] = $errors;
            }
        }

        return $nonAppliedRulesErrors;
    }

    /**
     * @param string $ruleName
     * @param array $parameters
     * @return array errors
     */
    public function applyRule(string $ruleName, array $parameters): array
    {
        foreach ($this->rules as $rule) {
            if ($rule->getName() === $ruleName) {
                return $rule->apply($parameters);
            }
        }

        return [];
    }
}
