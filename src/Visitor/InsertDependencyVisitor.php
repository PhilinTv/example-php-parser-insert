<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/4/18
 * Time: 1:16 AM
 */

namespace Sokyrko\DependencyInjector\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Sokyrko\DependencyInjector\Config\InjectorConfig;

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
     * @var \Sokyrko\DependencyInjector\Config\InjectorConfig
     */
    protected $injectorConfig;

    /**
     * @param string $methodName
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     */
    public function __construct(string $methodName, InjectorConfig $injectorConfig)
    {
        $this->methodName = $methodName;
        $this->injectorConfig = $injectorConfig;
    }

    /**
     * @param Node $node
     *
     * @return array|int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if ($this->isInsertPositionEnd() && $this->isRequiredArrayExpression($node, $this->methodName)) {
            /** @var \PhpParser\Node\Expr\Array_ $node */
            $node->items[] = $this->createArrayItemWithInstanceOf();

            return $node;

        }
            if (!$this->isRequiredArrayItem($node, $this->methodName)) {
            return null;
        }

        $currentArrayItemDependencyName = $node->value->class->toCodeString();

        switch ($currentArrayItemDependencyName) {
            case $this->injectorConfig->getBeforeClassName():
                return [$this->createArrayItemWithInstanceOf(), $node];
            case $this->injectorConfig->getAfterClassName():
                return [$node, $this->createArrayItemWithInstanceOf()];
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function isInsertPositionEnd(): bool
    {
        return $this->injectorConfig->getBeforeClassName() === null && $this->injectorConfig->getAfterClassName() === null;
    }

    /**
     * @return ArrayItem
     */
    protected function createArrayItemWithInstanceOf(): ArrayItem
    {
        return new ArrayItem(new New_(new FullyQualified($this->injectorConfig->getInsertInstanceOf())));
    }

    /**
     * @param Node $node
     * @param string $methodName
     *
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
     *
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
     *
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
