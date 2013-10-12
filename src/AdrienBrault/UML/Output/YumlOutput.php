<?php

namespace AdrienBrault\UML\Output;

use AdrienBrault\UML\Builder\FqcnCollection;
use AdrienBrault\UML\Node\ClassNode;
use AdrienBrault\UML\Node\FqcnNode;
use AdrienBrault\UML\Node\InterfaceNode;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class YumlOutput
{
    /**
     * @var array
     */
    private $namespacesColors;

    public function __construct(
        $namespacesColors = array(),
        $excludedNamespaces = array()
    ) {
        $this->namespacesColors = $namespacesColors;
    }

    public function create(FqcnCollection $fqcnCollection, $glue = ', ')
    {
        $statements = array();

        foreach ($fqcnCollection->all() as $node) {
            if ($node instanceof ClassNode) {
                $statements[] = sprintf(
                    '[%s]',
                    $this->format($node)
                );

                $statements = array_merge(
                    $statements,
                    $this->getStatements('^-.-', $node, $node->getImplementedInterfaces())
                );
                $statements = array_merge(
                    $statements,
                    $this->getStatements('^-', $node, array($node->getParent()))
                );
            } elseif ($node instanceof InterfaceNode) {
                $statements[] = sprintf(
                    '[%s]',
                    $this->format($node)
                );

                $statements = array_merge(
                    $statements,
                    $this->getStatements('^-', $node, $node->getParents())
                );
            }

            $statements = array_merge(
                $statements,
                $this->getStatements('uses -.->', $node, $node->getDependencies())
            );
        }

        foreach ($this->namespacesColors as $namespace => $color) {
            $statements[] = sprintf(
                '[note: %s{bg:%s}]',
                $this->formatFqcn($namespace),
                $color
            );
        }

        return join($glue, $statements);
    }

    private function formatFqcn($fqcn)
    {
        return str_replace('\\', str_repeat('\\', 4), $fqcn);
    }

    private function format(FqcnNode $node)
    {
        $name = $this->formatFqcn($node->getName());

        if ($node instanceof InterfaceNode) {
            $name = sprintf('<<%s>>', $node->getName());
        }

        if ($color = $this->getColor($node)) {
            $name .= sprintf('{bg:%s}', $color);
        }

        return $name;
    }

    private function getStatements($link, FqcnNode $node, array $parentNodes)
    {
        $parentNodes = array_filter($parentNodes);

        $statements = array();
        foreach ($parentNodes as $parentNode) {
            $statements[] = sprintf(
                '[%s]%s[%s]',
                $this->format($parentNode),
                $link,
                $this->format($node)
            );
        }

        return $statements;
    }

    private function getColor(FqcnNode $node)
    {
        foreach ($this->namespacesColors as $namespace => $color) {
            if (empty($namespace) || 0 === strpos($node->getName(), $namespace)) {
                return $color;
            }
        }

        return null;
    }
}
