<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/3/18
 * Time: 11:50 PM
 */

namespace Sokyrko\DependencyInjector\Rule;

use PhpParser\Node;
use PhpParser\NodeFinder;
use Sokyrko\DependencyInjector\Config\InjectorConfig;
use Sokyrko\DependencyInjector\Visitor\AddParentNodeVisitor;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\NodeTraverserInterface;

use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Sokyrko\DependencyInjector\Visitor\InsertDependencyVisitor;

class InsertReturnDependencyRule implements RuleInterface
{
    public const NAME = 'insertDependency';
    /**
     * @var NodeTraverserInterface
     */
    protected $nodeTraverser;

    /**
     * @var Parser
     */
    protected $parser;
    /**
     * @var Lexer
     */
    protected $lexer;

    /**
     * @param NodeTraverserInterface $nodeTraverser
     * @param Parser $parser
     * @param Lexer $lexer
     */
    public function __construct(NodeTraverserInterface $nodeTraverser, Parser $parser, Lexer $lexer)
    {
        $this->nodeTraverser = $nodeTraverser;
        $this->parser = $parser;
        $this->lexer = $lexer;
    }

    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array
     */
    public function apply(InjectorConfig $injectorConfig): array
    {
        [$fullFilePath, $className, $methodName] = $this->resolveTarget($injectorConfig->getMethodReference());

        try {
            $ast = $this->parser->parse(file_get_contents($fullFilePath));
            $oldTokens = $this->lexer->getTokens();
        } catch (Error $error) {
            return ["Parse error: {$error->getMessage()}"];
        }

        $ast = $this->nodeTraverser->traverse($ast);

        if ($this->isDependencyAlreadyExist($ast, $injectorConfig->getInsertInstanceOf())) {
            return ['Dependency already exist.'];
        }

        $this->nodeTraverser->addVisitor(new AddParentNodeVisitor());
        $this->nodeTraverser->addVisitor(new InsertDependencyVisitor($methodName, $injectorConfig));

        $newAst = $this->nodeTraverser->traverse($ast);

        $prettyPrinter = new Standard();
        $newCode = $prettyPrinter->printFormatPreserving($newAst, $ast, $oldTokens);

        if (defined('OUT_DIR')) {
            $this->writeToCustomDir($className, $newCode, OUT_DIR);
        } else {
            $this->writeToDir($newCode, $fullFilePath);
        }

        return [];
    }

    /**
     * @param string $newCode
     * @param string $fullFilePath
     *
     * @return void
     */
    protected function writeToDir(string $newCode, string $fullFilePath): void
    {
        file_put_contents($fullFilePath, $newCode);
    }

    /**
     * @param string $className
     * @param string $newCode
     * @param string $outDir
     *
     * @return void
     */
    protected function writeToCustomDir(string $className, string $newCode, string $outDir): void
    {
        $outFilePath = $outDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        $outDir = dirname($outFilePath);

        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        file_put_contents($outFilePath, $newCode);
    }


    /**
     * @param string $classMethodName
     *
     * @return array
     */
    protected function resolveTarget(string $classMethodName): array
    {
        [$className, $methodName] = explode('::', $classMethodName);
        $fullFilePath = (new \ReflectionClass($className))->getFileName();

        return [$fullFilePath, $className, $methodName];
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param array $ast
     * @param string $insertInstanceOf
     *
     * @return bool
     */
    protected function isDependencyAlreadyExist(array $ast, string $insertInstanceOf): bool
    {
        $dependency = (new NodeFinder())->findFirst($ast, function (Node $node) use ($insertInstanceOf) {
            if ($node->getType() === 'Expr_ArrayItem') {
                $className = $this->fixClassName($insertInstanceOf);

                return $className === $node->value->class->toCodeString();
            }
        });

        return $dependency !== null;
    }

    /**
     * @param string $className
     *
     * @return string
     */
    protected function fixClassName(string $className): string
    {
        if (strpos($className, '\\') === 0) {
            return $className;
        }

        return '\\' . $className;
    }
}
