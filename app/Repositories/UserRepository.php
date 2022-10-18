<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     */
    public function model()
    {
        return User::class;
    }

    public function usersListExceptOwn($id): Collection
    {
        $builder = $this->model;
        return $builder::with('currency')->where('id', '!=', $id)->get();
    }

    public function getUserById($id): User
    {
        $builder = $this->model;
        return $builder::with(['currency'])->find($id);
    }

    public function getAuthUser(): User
    {
        return auth()->user();
    }

    public function updateUser(int $userId, array $newDetails)
    {

        return $this->model::where('id', $userId)
            ->update([
                'wallet' => $newDetails['wallet']
            ]);
    }

    public function getSenderAndReceiverInfo($request): array
    {
        // Getting sender and receivers
        $users = User::with('currency')->whereIn('id', [$request['sender_id'], $request['receiver_id']])
            ->get();

        // Creating new array, and adding currency, wallet for sender and receiver
        $results = [];
        foreach ($users as $user) {
            if ($user->id == $request['sender_id']) {
                $results['sender']['id'] = $user->id;
                $results['sender']['currency'] = $user->currency->code;
                $results['sender']['wallet'] = $user->wallet;
            } else {
                $results['receiver']['id'] = $user->id;
                $results['receiver']['currency'] = $user->currency->code;
                $results['receiver']['wallet'] = $user->wallet;
            }
        }
        return  $results;
    }

    public function logoutApi(): void
    {
        auth()->user()->tokens()->delete();
    }
}
