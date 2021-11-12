<?php

namespace Nio\QueryBuilder;


/**
 * Build DELETE Statments
 * 
 * @author Carlos Bumba git:@CarlosNio
 */
class Delete
{
    use traits\CondictionTrait;
    use traits\JoinTrait;

    private $table;
    private $fields = '';
    private $order;
    private $limit;
    private $join;

    public function __construct($table)
    {
        if (is_array($table)) $table = implode(" , ", $table);
        $this->table = $table;
    }

    public function order(string $field, string $mode = 'ASC')
    {
        $this->order = "ORDER BY {$field} {$mode}";
        return $this;
    }

    public function limit($n)
    {
        $this->limit = "LIMIT {$n}";
        return $this;
    }

    public function ignore(bool $state = false)
    {
        $this->ignore = $state;
    }

    public function makeJoin()
    {
        $sql = $this->buildJoin();
        $sql = 'DELETE ' . $sql;
        $sql .= $this->ProcessCond();
        return [$sql, $this->map['PARAMS'] ?? null];
    }

    public function results()
    {
        // update statment
        $sql = "DELETE {$this->fields} FROM {$this->table} ";
        // condicitions
        $sql .= $this->ProcessCond();
        $sql .= ";";
        // data to insert in parameters
        return [$sql, $this->map['PARAMS'] ?? []];
    }
}