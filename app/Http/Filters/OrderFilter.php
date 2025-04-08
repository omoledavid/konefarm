<?php

namespace App\Http\Filters;

class OrderFilter extends QueryFilter
{
    public function include($value)
    {
        // Ensure $value is an array
        $includes = is_array($value) ? $value : explode(',', $value);

        return $this->builder->with($includes);
    }
    public function status($value)
    {
        return $this->builder->where('status', $value);
    }
}
