<?php

use AdrienBrault\UML\Builder\FqcnCollection;
use AdrienBrault\UML\Finder\Finder;
use AdrienBrault\UML\Output\YumlOutput;
use AdrienBrault\UML\Parser\Visitor\DependencyBuilder;
use AdrienBrault\UML\Parser\Visitor\FqcnBuilder;

require __DIR__ . '/vendor/autoload.php';

$finder = new Finder(array(
    '/Users/adrienbrault/Developer/php/Hateoas/src',
    //'/Users/adrienbrault/Developer/php/serializer/src',
));

$parser = new PHPParser_Parser(new PHPParser_Lexer());
$traverser = new PHPParser_NodeTraverser;
$traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver());

$stmtsCollection = array();

foreach ($finder as $file) { /** @var $file SplFileInfo */
    $stmts = $parser->parse(file_get_contents($file->getRealPath()));
    $stmts = $traverser->traverse($stmts);

    $stmtsCollection[] = $stmts;
}

$fqcnCollection = new FqcnCollection();

$traverser = new PHPParser_NodeTraverser;
$traverser->addVisitor(new FqcnBuilder($fqcnCollection));

foreach ($stmtsCollection as $stmts) {
    $traverser->traverse($stmts);
}

$traverser = new PHPParser_NodeTraverser;
$traverser->addVisitor(new DependencyBuilder($fqcnCollection));

foreach ($stmtsCollection as $stmts) {
    $traverser->traverse($stmts);
}

$output = new YumlOutput(array(
    //'Hateoas' => 'green',
    //'JMS\\Serializer' => 'orange',
    //'Metadata' => 'gray',
    //'Symfony' => 'blue',

    //'Hateoas\\Configuration' => 'green',
    //'Hateoas\\Serializer' => 'orange',
    //'Hateoas' => 'gray',

    'Hateoas' => null,
    '' => 'gray',
));
$result = $output->create($fqcnCollection, "\n");
echo $output->create($fqcnCollection, "\n");
$client = new \Guzzle\Http\Client('http://yuml.me');
$request = $client->post(
    '/diagram/nofunky;dir:LR;/class/short_url/', // nofunky, plain, scruffy // LR,TB,RL // scale:100;
    array(),
    array(
        'dsl_text' => $result,
    )
);
$response = $request->send();

echo sprintf('http://yuml.me/edit/%s', $response->getBody(true)).PHP_EOL;
echo sprintf('http://yuml.me/%s.png', $response->getBody(true)).PHP_EOL;
