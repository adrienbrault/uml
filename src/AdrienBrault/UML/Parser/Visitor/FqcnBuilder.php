<?php

namespace AdrienBrault\UML\Parser\Visitor;

use AdrienBrault\UML\Builder\FqcnCollection;
use AdrienBrault\UML\Node\ClassNode;
use AdrienBrault\UML\Node\FqcnNode;
use AdrienBrault\UML\Node\InterfaceNode;
use PHPParser_Node;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Interface;
use PHPParser_NodeVisitorAbstract;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class FqcnBuilder extends PHPParser_NodeVisitorAbstract
{
    /**
     * @var FqcnCollection
     */
    private $fqcnCollection;

    public function __construct(FqcnCollection $fqcnCollection)
    {
        $this->fqcnCollection = $fqcnCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(PHPParser_Node $node)
    {
        if ($node instanceof PHPParser_Node_Stmt_Class) {
            $this->fqcnCollection->getOrCreateClass((string) $node->namespacedName);
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            $this->fqcnCollection->getOrCreateInterface((string) $node->namespacedName);
        }
    }
}
