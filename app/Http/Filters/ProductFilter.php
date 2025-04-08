<?php

namespace App\Http\Filters;

class ProductFilter extends QueryFilter
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
    public function user_id($value)
    {
        return $this->builder->where('user_id', $value);
    }
    public function id($value)
    {
        return $this->builder->whereIn('id', explode(',', $value));
    }
    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }
    public function description($value)
    {
        return $this->builder->where('description', 'like', '%' . $value . '%');
    }
    public function createdAt($value)
    {
        $dates = explode(',', $value);
        if(count($dates) > 1){
            return $this->builder->whereBetween('created_at', $dates);
        }
        return $this->builder->whereDate('created_at', $value);
    }
    public function updatedAt($value)
    {
        $dates = explode(',', $value);
        if(count($dates) > 1){
            return $this->builder->whereBetween('updated_at', $dates);
        }
        return $this->builder->whereDate('updated_at', $value);
    }
    protected function paginate($value)
    {
        $this->builder->paginate($value);
    }
}
