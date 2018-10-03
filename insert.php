#!/usr/bin/env php
<?php

use App\NodeEditor;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser\Php7;

include 'vendor/autoload.php';

const APP_DIR = __DIR__;
const OUT_DIR = APP_DIR . DIRECTORY_SEPARATOR . 'out/';

$lexer = new Emulative([
    'usedAttributes' => [
        'comments',
        'startLine', 'endLine',
        'startTokenPos', 'endTokenPos',
    ],
]);

$parser = new Php7($lexer);

$nodeTraverser = new NodeTraverser();
$nodeTraverser->addVisitor(new CloningVisitor());
$nodeTraverser->addVisitor(new NameResolver());

$config = require './config.php';;
$rules = [
    new App\Rule\InsertDependencyRule($nodeTraverser, $parser, $lexer),
];

$errors = (new NodeEditor($rules))->apply($config);

print_r($errors);
