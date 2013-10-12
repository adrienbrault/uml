<?php

namespace AdrienBrault\UML\Node;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class InterfaceNode extends FqcnNode
{
    /**
     * @var InterfaceNode[] Only the interface that this interface implements, not the one its parents implement
     */
    private $parents;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->parents = array();
    }

    /**
     * @return InterfaceNode[]
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param InterfaceNode[] $parents
     */
    public function setParents(array $parents)
    {
        $this->parents = $parents;
    }
}
