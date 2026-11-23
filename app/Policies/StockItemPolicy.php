<?php

namespace App\Policies;

use App\Models\StockItem;
use App\Models\User;

class StockItemPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, StockItem $stockItem): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, StockItem $stockItem): bool
    {
        return $user->id === $stockItem->user_id;
    }

    public function delete(User $user, StockItem $stockItem): bool
    {
        return $user->id === $stockItem->user_id;
    }
}