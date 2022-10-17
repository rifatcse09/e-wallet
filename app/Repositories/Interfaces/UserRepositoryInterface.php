<?php

namespace App\Repositories\Interfaces;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function getUserById(int $id): User;
    public function getAuthUser(): User;
    public function updateUser(int $userId, array $newDetails): User;
    public function getSenderAndReceiverInfo($request): array;
}
