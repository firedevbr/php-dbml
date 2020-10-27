<?php


namespace Dbml\Dbml\Model\Table;


class Index
{
    const TYPE_BTREE = 'btree';

    const TYPE_GIN   = 'gin';

    const TYPE_GIST  = 'gist';

    const TYPE_HASH  = 'hash';

    /**
     * @var boolean
     */
    private $primaryKey;

    /**
     * @var null|string
     */
    private $type = null;

    /**
     * @var boolean
     */
    private $unique;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * Index constructor.
     * @param array $columns
     * @param bool|null $primaryKey
     * @param bool|null $unique
     * @param string|null $type
     */
    public function __construct(array $columns, ?bool $primaryKey = false, ?bool $unique = false, ?string $type = null)
    {
        $this->primaryKey = $primaryKey;
        $this->unique     = $unique;
        $this->setColumns($columns);
        $this->type       = $type;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

    /**
     * @param bool $primaryKey
     */
    public function setPrimaryKey(bool $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     */
    public function setUnique(bool $unique): void
    {
        $this->unique = $unique;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns(array $columns): void
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    public function addColumn(Column $column): self
    {
        $this->columns[] = $column;
        $column->addIndex($this);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}