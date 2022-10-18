<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Traits\RespondsWithHttpStatusTrait;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    use RespondsWithHttpStatusTrait;

    private $_UserRepos;

    public function __construct(UserRepositoryInterface $UserRepos)
    {
        $this->_UserRepos = $UserRepos;
    }

    public function userProfile(): JsonResponse
    {
        $userInfo = $this->_UserRepos->getAuthUser();

        return is_null($userInfo) ? $this::failure(__('oauth.user_not_found')) : $this::success($userInfo, __('messages.success_message'));
    }
    public function usersListExceptOwn(): JsonResponse
    {
        $userInfo = $this->_UserRepos->getAuthUser();
        $userList = $this->_UserRepos->usersListExceptOwn($userInfo->id);

        return is_null($userList) ? $this::failure(__('oauth.user_not_found')) : $this::success($userList, __('messages.success_message'));
    }
}
