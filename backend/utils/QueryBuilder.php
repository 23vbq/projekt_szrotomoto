<?php
class QueryBuilder {
    private PDO $pdo;

    private array $select = [];
    private ?string $from = null;
    private array $where = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $params = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function select(string $select): self
    {
        $this->select = [$select];
        return $this;
    }

    public function addSelect(string $select): self
    {
        $this->select[] = $select;
        return $this;
    }

    public function from(string $table): self
    {
        $this->from = $table;
        return $this;
    }

    public function where(string $condition): self
    {
        $this->where = [
            [
                'type' => null,
                'condition' => $condition
            ]
        ];
        return $this;
    }

    public function andWhere(string $condition): self
    {
        $this->where[] = [
            'type' => 'AND',
            'condition' => $condition
        ];
        return $this;
    }

    public function orWhere(string $condition): self
    {
        $this->where[] = [
            'type' => 'OR',
            'condition' => $condition
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = ["$column $direction"];
        return $this;
    }

    public function addOrderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function setParameter(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function execute(): PDOStatement
    {
        $query = $this->buildQuery();
        $stmt = $this->pdo->prepare($query);
        foreach ($this->params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    private function buildQuery(): string
    {
        if (empty($this->select)) {
            throw new Exception('No columns selected for query');
        }

        $query = 'SELECT ' . implode(', ', $this->select);

        if ($this->from !== null) {
            $query .= ' FROM ' . $this->from;
        }

        if (!empty($this->where)) {
            $whereClauses = [];
            foreach ($this->where as $index => $condition) {
                $prefix = $index === 0 ? '' : ' ' . $condition['type'] . ' ';
                $whereClauses[] = $prefix . $condition['condition'];
            }
            $query .= ' WHERE ' . implode('', $whereClauses);
        }

        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $query .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $query .= ' OFFSET ' . $this->offset;
        }

        return $query;
    }
}