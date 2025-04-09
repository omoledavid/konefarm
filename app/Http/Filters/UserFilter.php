<?php

namespace App\Http\Filters;

class UserFilter extends QueryFilter
{
    public function include($value)
    {
        // Ensure $value is an array
        $includes = is_array($value) ? $value : explode(',', $value);

        return $this->builder->with($includes);
    }
    public function id($value)
    {
        return $this->builder->where('id', $value);
    }
}
