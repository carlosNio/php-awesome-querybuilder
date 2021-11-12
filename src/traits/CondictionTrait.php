<?php

namespace Nio\QueryBuilder\traits;


/**
 * Condictions Where and Logics will be created here
 * 
 * @author Carlos Bumba git:@CarlosNio
 */

trait CondictionTrait
{
    private $map;
    private $case;

    public function where(string $str, array $params = [])
    {
        $this->map['WHERE'] = $str;
        $this->map['PARAMS'] = array_merge($this->map['PARAMS'] ?? [],  $params);
        return $this;
    }

    public function and(string $str, array $params = [])
    {
        $this->map['LOGIC'][] = ['AND', $str];
        $this->map['PARAMS'] = array_merge($this->map['PARAMS'] ?? [],  $params);
        return $this;
    }

    public function or(string $str, array $params = [])
    {
        $this->map['LOGIC'][] = ['OR', $str];
        $this->map['PARAMS'] = array_merge($this->map['PARAMS'] ?? [],  $params);
        return $this;
    }


    public function ProcessCond()
    {
        $sql = '';
        $matrix = $this->map;

        // WHERE
        if (isset($matrix['WHERE'])) {
            $sql .= " WHERE " . $matrix['WHERE'];
        }

        // LOGIC
        if (isset($matrix['LOGIC'])) {
            foreach ($matrix['LOGIC'] as $value) {
                $type = $value[0];
                $string = $value[1];
                $sql .= " {$type} {$string} ";
            }
        }

        return $sql;
    }
}
