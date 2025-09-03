<?php

namespace Fmk\Database;

class Builder
{
    protected $wheres = [];

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof \Closure) {
            $query = new self();
            $column($query);
            $type = 'nested';
            $this->wheres[] = compact('type', 'query', 'boolean');
            return $this;
        }

        $type = 'basic';
        $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }

    protected function compilerBuilder($prefix = '__w')
    {
        $sql = [];
        $data = [];

        foreach ($this->wheres as $key => $where) {
            if ($where['type'] === 'basic') {

                // --- tratamento especial para NULL ---
                if (is_null($where['value']) && in_array(strtoupper($where['operator']), ['=', 'IS'])) {
                    $condition = "{$where['column']} IS NULL";
                } elseif (is_null($where['value']) && in_array(strtoupper($where['operator']), ['!=', '<>', 'IS NOT'])) {
                    $condition = "{$where['column']} IS NOT NULL";
                } else {
                    $param = ":$prefix$key";
                    $condition = "{$where['column']} {$where['operator']} $param";
                    $data[$prefix . $key] = $where['value'];
                }

            } else {
                [$nested, $nesteddata] = $where['query']->compilerBuilder($prefix . $key . "__");
                $condition = "($nested)";
                $data = array_merge($data, $nesteddata);
            }

            if ($key) {
                $condition = strtoupper($where['boolean']) . " " . $condition;
            }

            $sql[] = $condition;
        }

        return [implode(" ", $sql), $data];
    }

    public function compiler()
    {
        if (count($this->wheres)) {
            [$sql, $data] = $this->compilerBuilder();
            return [" WHERE $sql", $data];
        }
        return ["", []];
    }

    public function isEmpty()
    {
        return empty($this->wheres);
    }
}
