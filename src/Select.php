<?php

namespace Nio\QueryBuilder;

use Exception;

use Nio\QueryBuilder\traits\CondictionTrait;
use Nio\QueryBuilder\traits\JoinTrait;
use Nio\QueryBuilder\traits\CaseTrait;

/**
 * Build Select Statments
 * 
 * @author Carlos Bumba git:@CarlosNio
 */
final class Select extends Utils
{
    use CondictionTrait;
    use JoinTrait;
    use CaseTrait;

    protected $table;
    protected $distinct;
    protected $fields;
    protected $vars;
    protected $join;

    public function __construct(string $table, bool $distinct = false)
    {
        $this->table = $table;
        $this->distinct = $distinct ? 'DISTINCT' : '';
    }

    // 
    public function having(string $str, array $params = [])
    {
        $this->vars['HAVING'] = $str;
    }

    public function limit($n)
    {
        $this->vars['LIMIT'] = $n;
        return $this;
    }

    public function order($value)
    {
        $this->vars['ORDER BY'] = $value;
        return $this;
    }

    public function group($value)
    {
        $this->vars['GROUP BY'] = $value;
        return $this;
    }

    public function makeJoin(string $type)
    {
        $join_types = ['inner', 'left', 'right', 'full outer'];

        // case condictions
        if (isset($this->map['WHEN']))
            $case = " {$this->caseCond()} , ";

        // invalid join type
        if (!in_array($type, $join_types)) {
            $only = implode(" , ", $join_types);
            throw new Exception("Invalid Join Type '{$type}' (only: {$only})");
        }

        if ($type != 'inner') $type .= " outer";
        $type = strtoupper("{$type} join");
        // sql
        $sql = "SELECT ";
        if (isset($case)) $sql .= $case;
        $sql .= $this->buildJoin();
        //  insert the join type
        $sql = str_replace("JOIN", $type, $sql);
        $this->sql = $sql;
    }

    public function leftSemiJoin(string $field1, string $field2, bool $anti = false)
    {
        return $this->semiJoin($field1, $field2, $anti);
    }

    public function rightSemiJoin(string $field1, string $field2, bool $anti = false)
    {
        return $this->semiJoin($field1, $field2, $anti, true);
    }

    // 
    public function results()
    {
        // condictions
        $conds = $this->ProcessCond();

        // if already was builded a sql return it
        if (isset($this->sql)) {
            $this->sql .= $conds;
            $this->modifiers($this->sql);
            return [$this->sql, $this->map['PARAMS'] ?? null];
        }

        // result fields
        if (!isset($this->fields))
            $fields = '*';
        else
            $fields = $this->fields;
        // case condictions
        if (isset($this->map['WHEN'])) {
            $fields = " {$this->caseCond()} , {$fields}";
        }

        $sql = "SELECT {$this->distinct} {$fields} FROM {$this->table} ";
        // append the condictions
        $sql .= $conds;
        // modifiers
        $this->modifiers($sql);

        // result
        return [$sql . " ;", $this->map['PARAMS'] ?? null];
    }
}
