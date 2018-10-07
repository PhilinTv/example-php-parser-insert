<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/4/18
 * Time: 12:42 AM
 */

namespace Sokyrko\DependencyInjector\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddParentNodeVisitor extends NodeVisitorAbstract
{
    private $stack;

    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }

    public function enterNode(Node $node)
    {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}