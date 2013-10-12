<?php

namespace AdrienBrault\UML\Node;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
abstract class FqcnNode
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \SplObjectStorage
     */
    private $dependencies;

    public function __construct($name)
    {
        $this->name = $name;
        $this->dependencies = new \SplObjectStorage();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function addDependency(FqcnNode $dependency)
    {
        $this->dependencies->attach($dependency);
    }

    public function addDependencies(array $dependencies)
    {
        foreach ($dependencies as $dependency) {
            $this->addDependency($dependency);
        }
    }

    /**
     * @return FqcnNode[]
     */
    public function getDependencies()
    {
        return iterator_to_array($this->dependencies);
    }
}
