<?php

namespace AdrienBrault\UML\Parser\Visitor;

use AdrienBrault\UML\Parser\FqcnTraverserDelegateInterface;
use PHPParser_Node;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Interface;
use PHPParser_NodeVisitorAbstract;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class FqcnTraverser extends PHPParser_NodeVisitorAbstract
{
    /**
     * @var FqcnTraverserDelegateInterface
     */
    private $delegate;

    public function __construct(FqcnTraverserDelegateInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(PHPParser_Node $node)
    {
        if ($node instanceof PHPParser_Node_Stmt_Class) {
            $this->delegate->enterClass((string) $node->namespacedName, $node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            $this->delegate->enterInterface((string) $node->namespacedName, $node);
        }
    }

    public function leaveNode(PHPParser_Node $node)
    {
        if ($node instanceof PHPParser_Node_Stmt_Class) {
            $this->delegate->leaveClass((string) $node->namespacedName, $node);
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            $this->delegate->leaveInterface((string) $node->namespacedName, $node);
        }
    }
}
