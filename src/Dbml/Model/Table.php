<?php

declare(strict_types=1);

namespace Dbml\Dbml\Model;

use Dbml\Dbml\Model\Table\Column;
use Dbml\Dbml\Model\Table\Index;
use Dbml\Dbml\Model\Table\Relationship;

/**
 * Class Table
 * @package Dbml\Dbml
 */
class Table
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string|null
     */
    public $alias = null;

    /**
     * @var Column[]
     */
    public $columns = [];

    /**
     * @var Relationship[]
     */
    public $relationships = [];

    /**
     * @var Index[]
     */
    public $indexes = [];

    /**
     * Table constructor.
     * @param string $name
     * @param string|null $alias
     * @param Column[] $columns
     * @param Relationship[] $relationships
     */
    public function __construct(
        string $name,
        ?string $alias = null,
        array $columns = [],
        array $relationships = [],
        array $indexes = []
    ) {
        $this->name          = $name;
        $this->alias         = $alias;
        $this->columns       = $columns;
        $this->relationships = $relationships;
        $this->indexes       = $indexes;
    }

    /**
     * @param Column $column
     * @return Table
     */
    public function addColumn(Column $column): Table
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param Relationship $relationship
     * @return Table
     */
    public function addRelationship(Relationship $relationship): Table
    {
        $this->relationships[] = $relationship;

        return $this;
    }

    public function addIndex(Index $index): Table
    {
        $this->indexes[] = $index;

        return $this;
    }
}