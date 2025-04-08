<?php

namespace App\Enums;

enum ProductStatus: string
{
    const INACTIVE = 'inactive';
    const ACTIVE = 'active';
    const REJECTED = 'rejected';
}
