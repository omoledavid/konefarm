<?php

namespace App\Enums;

enum OrderStatus: int
{
    const PENDING = 'pending';
    const PAID = 'paid';
    const SHIPPED = 'shipped';
    const DELIVERED = 'delivered';
    const CANCELLED = 'cancelled';
}
