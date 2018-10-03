<?php
/**
 * Created by PhpStorm.
 * User: andreyons
 * Date: 10/3/18
 * Time: 11:50 PM
 */

namespace App\Rule;

use App\Visitor\AddParentNodeVisitor;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\NodeTraverserInterface;

use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use App\Visitor\InsertDependencyVisitor;

class InsertDependencyRule implements RuleInterface
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
     * @param array $parameters
     * @return array
     */
    public function apply(array $parameters): array
    {
        $errors = [];

        foreach ($parameters as $classMethodName => $conditions) {
            $errors[$classMethodName] = $this->insertDependency($classMethodName, $conditions);
        }

        return $errors;
    }

    /**
     * @param string $classMethodName
     * @param array $conditions
     * @return array applying errors
     */
    protected function insertDependency(string $classMethodName, array $conditions): array
    {
        [$fullFilePath, $className, $methodName] = $this->resolveTarget($classMethodName);

        try {
            $ast = $this->parser->parse(file_get_contents($fullFilePath));
            $oldTokens = $this->lexer->getTokens();
        } catch (Error $error) {
            return ["Parse error: {$error->getMessage()}"];
        }

        $this->nodeTraverser->addVisitor(new AddParentNodeVisitor());
        $this->nodeTraverser->addVisitor(new InsertDependencyVisitor($methodName, $conditions));

        $newAst = $this->nodeTraverser->traverse($ast);

        $prettyPrinter = new Standard();
        $newCode = $prettyPrinter->printFormatPreserving($newAst, $ast, $oldTokens);

        if (defined('OUT_DIR')) {
            $outFilePath = OUT_DIR . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
            $outDir = dirname($outFilePath);

            if (!is_dir($outDir)) {
                mkdir($outDir, 0755, true);
            }

            file_put_contents($outFilePath, $newCode);
        }

        return [];
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
}
