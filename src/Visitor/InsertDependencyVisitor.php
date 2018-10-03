<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/4/18
 * Time: 1:16 AM
 */

namespace App\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

class InsertDependencyVisitor extends NodeVisitorAbstract
{
    public const CONDITION_INSTANCE_OF = 'instanceOf';
    public const CONDITION_AFTER = 'after';
    public const CONDITION_BEFORE = 'before';

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var array
     */
    protected $conditions;

    /**
     * @param string $methodName
     * @param array $conditions
     */
    public function __construct(string $methodName, array $conditions)
    {
        $this->methodName = $methodName;
        $this->conditions = array_merge($this->getDefaultConditions(), $conditions);
    }


    /**
     * @return array
     */
    protected function getDefaultConditions(): array
    {
        return [
            static::CONDITION_AFTER => '',
            static::CONDITION_BEFORE => '',
            static::CONDITION_INSTANCE_OF => '',
        ];
    }

    /**
     * @param Node $node
     * @return array|int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$this->isRequiredArrayItem($node, $this->methodName)) {
            return null;
        }

        /** @var $node ArrayItem */
        $currentArrayItemDependencyName = $node->value->class->toCodeString();

        switch ($currentArrayItemDependencyName) {
            case $this->conditions[static::CONDITION_BEFORE]:
                return [$this->createArrayItemWithInstanceOf(), $node];
            case $this->conditions[static::CONDITION_AFTER]:
                return [$node, $this->createArrayItemWithInstanceOf()];
        }

        return null;
    }

    /**
     * @return ArrayItem
     */
    protected function createArrayItemWithInstanceOf(): ArrayItem
    {
        return new ArrayItem(new New_(new FullyQualified($this->conditions[static::CONDITION_INSTANCE_OF])));
    }

    /**
     * @param Node $node
     * @param string $methodName
     * @return bool
     */
    protected function isRequiredArrayItem(Node $node, string $methodName): bool
    {
        if ($node->getType() !== 'Expr_ArrayItem') {
            return false;
        }

        $parent = $node->getAttribute('parent');
        $hasParent = $parent !== null;

        return $hasParent && $this->isRequiredArrayExpression($parent, $methodName);
    }

    /**
     * @param Node $node
     *
     * @param string $methodName
     *
     * @return bool
     */
    protected function isRequiredArrayExpression(Node $node, string $methodName): bool
    {
        return $this->isArrayExpressionType($node)
            && $this->isParentReturn($node)
            && $this->isParentMethodRight($node, $methodName);
    }

    /**
     * @param Node $node
     * @param string $methodName
     * @return bool
     */
    protected function isParentMethodRight(Node $node, string $methodName): bool
    {
        /** @var Node|Node\Stmt\ClassMethod|null $parent */
        $parent = $node->getAttribute('parent')->getAttribute('parent');
        $hasParent = $parent !== null;

        if (!$hasParent || $parent->getType() !== 'Stmt_ClassMethod') {
            return false;
        }

        return $parent->name->toString() === $methodName;
    }

    /**
     * @param Node $node
     * @return bool
     */
    protected function isParentReturn(Node $node): bool
    {
        /** @var Node|null $parent */
        $parent = $node->getAttribute('parent');
        $hasParent = $parent !== null;

        return $hasParent && $parent->getType() === 'Stmt_Return';
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    public function isArrayExpressionType(Node $node): bool
    {
        return $node->getType() === 'Expr_Array';
    }
}
