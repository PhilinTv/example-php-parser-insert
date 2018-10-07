<?php

namespace Sokyrko\DependencyInjector;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser\Php7;
use Sokyrko\DependencyInjector\Config\InjectorConfig;
use Sokyrko\DependencyInjector\Rule\InsertReturnDependencyRule;

class DependencyInjectorFacade implements DependencyInjectorFacadeInterface
{
    /**
     * @param \Sokyrko\DependencyInjector\Config\InjectorConfig $injectorConfig
     *
     * @return array errors
     */
    public function inject(InjectorConfig $injectorConfig): array
    {
        return $this->createNodeEditor()->apply($injectorConfig);
    }

    /**
     * @return \Sokyrko\DependencyInjector\NodeEditor
     */
    protected function createNodeEditor()
    {
        return new NodeEditor(
            $this->getRules()
        );
    }

    /**
     * @return array
     */
    protected function getRules(): array
    {
        $nodeTraverser = $this->createNodeTraverser();
        $lexer = $this->createLexer();
        $parser = $this->createParser($lexer);

        return [
            new InsertReturnDependencyRule($nodeTraverser, $parser, $lexer),
        ];
    }

    /**
     * @return \PhpParser\NodeTraverser
     */
    protected function createNodeTraverser()
    {
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new CloningVisitor());
        $nodeTraverser->addVisitor(new NameResolver());

        return $nodeTraverser;
    }

    /**
     * @return \PhpParser\Lexer
     */
    protected function createLexer()
    {
        return new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
    }

    /**
     * @param \PhpParser\Lexer $lexer
     *
     * @return \PhpParser\Parser\Php7
     */
    protected function createParser($lexer)
    {
        return new Php7($lexer);
    }
}
