<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Traits\RespondsWithHttpStatusTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Repositories\Interfaces\UserRepositoryInterface;

final class AuthController extends Controller

{
    use RespondsWithHttpStatusTrait;

    public function  __construct(UserRepositoryInterface $UserRepos)
    {
        $this->_UserRepos = $UserRepos;
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->isVerifiedUser($request) ?
            $this::success($success['token'] = $this->getAccessToken(), __('messages.success_message')) :
            $this::failure('Unauthorised.', ['error' => __('oauth.user_not_found')]);
    }

    public function getAccessToken(): string
    {
        return Auth::user()->createToken('MyApp')->accessToken;
    }

    public function isVerifiedUser($request): bool
    {
        return Auth::attempt(['email' => $request->email, 'password' => $request->password]);
    }

    public function logout(): JsonResponse
    {
        $this->_UserRepos->logoutApi();

        return  $this::success(null, __('oauth.logout_success'), Response::HTTP_NOT_FOUND);
    }

    // /**
    //  * Display the authenticated user details.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {

    //     $user = User::find($id);

    //     if (is_null($user)) {
    //         return $this->sendError('User not found.');
    //     }

    //     return $this->sendResponse($user, 'User retrieved successfully.');
    // }
}
