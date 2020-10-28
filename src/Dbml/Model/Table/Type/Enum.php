<?php


namespace Dbml\Dbml\Model\Table\Type;

/**
 * Class Enum
 * @package Dbml\Dbml\Model\Table\Type
 */
class Enum
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $values;

    /**
     * Enum constructor.
     * @param array $values
     */
    public function __construct(string $id, array $values)
    {
        $this->id     = $id;
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}