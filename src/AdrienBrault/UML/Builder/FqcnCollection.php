<?php

namespace AdrienBrault\UML\Builder;

use AdrienBrault\UML\Node\ClassNode;
use AdrienBrault\UML\Node\FqcnNode;
use AdrienBrault\UML\Node\InterfaceNode;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class FqcnCollection
{
    /**
     * @var FqcnNode[]
     */
    private $nodes;

    public function __construct()
    {
        $this->nodes = array();
    }

    public function get($fqcn)
    {
        if (!$this->has($fqcn)) {
            throw new \InvalidArgumentException(sprintf('Unknown fqcn "%s".', $fqcn));
        }

        return $this->nodes[$fqcn];
    }

    public function has($fqcn)
    {
        return isset($this->nodes[$fqcn]);
    }

    public function add(FqcnNode $node)
    {
        $this->nodes[$node->getName()] = $node;

        return $node;
    }

    public function all()
    {
        return $this->nodes;
    }

    /**
     * @param  string          $fqcn
     * @return ClassNode
     * @throws \LogicException
     */
    public function getOrCreateClass($fqcn)
    {
        if ($this->has($fqcn)) {
            $class = $this->get($fqcn);

            if ($class instanceof InterfaceNode) {
                throw new \LogicException(sprintf('"%s" is already known as an interface'));
            }

            return $class;
        }

        return $this->add(new ClassNode($fqcn));
    }

    /**
     * @param  string          $fqcn
     * @return InterfaceNode
     * @throws \LogicException
     */
    public function getOrCreateInterface($fqcn)
    {
        if ($this->has($fqcn)) {
            $interface = $this->get($fqcn);

            if ($interface instanceof ClassNode) {
                throw new \LogicException(sprintf('"%s" is already known as a class'));
            }

            return $interface;
        }

        return $this->add(new InterfaceNode($fqcn));
    }

    public function getOrCreateGuessing($fqcn)
    {
        if ($this->has($fqcn)) {
            return $this->get($fqcn);
        }

        if (class_exists($fqcn)) {
            return $this->getOrCreateClass($fqcn);
        } elseif (interface_exists($fqcn)) {
            return $this->getOrCreateInterface($fqcn);
        }

        throw new \RuntimeException(sprintf('Impossible to know wheter "%s" is a class or an interface.', $fqcn));
    }
}
