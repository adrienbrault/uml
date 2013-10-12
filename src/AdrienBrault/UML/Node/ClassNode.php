<?php

namespace AdrienBrault\UML\Node;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ClassNode extends FqcnNode
{
    /**
     * @var ClassNode
     */
    private $parent;

    /**
     * @var InterfaceNode[] Only the interfaces that the class implements, not the interface its parents or interfaces extend.
     */
    private $implementedInterfaces;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->implementedInterfaces = array();
    }

    /**
     * @return ClassNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ClassNode $parent
     */
    public function setParent(ClassNode $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return InterfaceNode[]
     */
    public function getImplementedInterfaces()
    {
        return $this->implementedInterfaces;
    }

    /**
     * @param InterfaceNode[] $interfaces
     */
    public function setImplementedInterfaces(array $interfaces)
    {
        $this->implementedInterfaces = $interfaces;
    }
}
