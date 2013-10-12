<?php

namespace AdrienBrault\UML\Parser\Visitor;

use AdrienBrault\UML\Builder\FqcnCollection;
use AdrienBrault\UML\Node\FqcnNode;
use PHPParser_Node;
use PHPParser_Node_Expr_ClassConstFetch;
use PHPParser_Node_Expr_Instanceof;
use PHPParser_Node_Expr_New;
use PHPParser_Node_Expr_StaticCall;
use PHPParser_Node_Expr_StaticPropertyFetch;
use PHPParser_Node_Name;
use PHPParser_Node_Name_FullyQualified;
use PHPParser_Node_Param;
use PHPParser_Node_Stmt_Catch;
use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Interface;
use PHPParser_NodeVisitorAbstract;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class DependencyBuilder extends PHPParser_NodeVisitorAbstract
{
    /**
     * @var FqcnCollection
     */
    private $fqcnCollection;

    /**
     * @var FqcnNode
     */
    private $currentNode;

    public function __construct(FqcnCollection $fqcnCollection)
    {
        $this->fqcnCollection = $fqcnCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function enterNode(PHPParser_Node $node)
    {
        if ($node instanceof PHPParser_Node_Stmt_Class || $node instanceof PHPParser_Node_Stmt_Interface) {
            $this->currentNode = $this->fqcnCollection->get((string) $node->namespacedName);
        }

        if ($node instanceof PHPParser_Node_Stmt_Class) {
            if ($node->extends) {
                $this->currentNode->setParent(
                    $this->fqcnCollection->getOrCreateClass((string) $node->extends)
                );
            }

            $this->currentNode->setImplementedInterfaces(
                $this->getImplementedInterfaces($node)
            );
        } elseif ($node instanceof PHPParser_Node_Stmt_Interface) {
            $this->currentNode->setParents(
                $this->getExtendedInterfaces($node)
            );
        }

        if ($node instanceof PHPParser_Node_Param && $node->type instanceof PHPParser_Node_Name) {
            $this->addDependencyGuessing($node->type);
        } elseif (
            $node instanceof PHPParser_Node_Expr_StaticCall
            || $node instanceof PHPParser_Node_Expr_StaticPropertyFetch
            || $node instanceof PHPParser_Node_Expr_New
        ) {
            $this->addClassDependency($node->class);
        } elseif ($node instanceof PHPParser_Node_Stmt_Catch) {
            $this->addClassDependency($node->type);
        } elseif (
            $node instanceof PHPParser_Node_Expr_ClassConstFetch
            || $node instanceof PHPParser_Node_Expr_Instanceof
        ) {
            $this->addDependencyGuessing($node->class);
        }
    }

    public function leaveNode(PHPParser_Node $node)
    {
        if ($node instanceof PHPParser_Node_Stmt_Class || $node instanceof PHPParser_Node_Stmt_Interface) {
            $this->currentNode = null;
        }
    }

    private function getImplementedInterfaces(PHPParser_Node_Stmt_Class $classNode)
    {
        if (!$classNode->implements) {
            return array();
        }

        $implementedInterfaces = array();
        foreach ($classNode->implements as $implement) {
            $implementedInterfaces[] = $this->fqcnCollection->getOrCreateInterface((string) $implement);
        }

        return $implementedInterfaces;
    }

    private function getExtendedInterfaces(PHPParser_Node_Stmt_Interface $classNode)
    {
        if (!$classNode->extends) {
            return array();
        }

        $extendedInterfaces = array();
        foreach ($classNode->extends as $extend) {
            $extendedInterfaces[] = $this->fqcnCollection->getOrCreateInterface((string) $extend);
        }

        return $extendedInterfaces;
    }

    private function addClassDependency(PHPParser_Node_Name $node)
    {
        if (!$node instanceof PHPParser_Node_Name_FullyQualified) {
            return;
        }

        $this->currentNode->addDependency(
            $this->fqcnCollection->getOrCreateClass((string) $node)
        );
    }

    private function addDependencyGuessing(PHPParser_Node_Name $node)
    {
        if (!$node instanceof PHPParser_Node_Name_FullyQualified) {
            return;
        }

        try {
            $this->currentNode->addDependency(
                $this->fqcnCollection->getOrCreateGuessing((string) $node)
            );
        } catch (\RuntimeException $e) {
            // TODO
        }
    }
}
