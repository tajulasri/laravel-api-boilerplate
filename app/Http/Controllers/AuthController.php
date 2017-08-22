<?php

namespace App\Http\Controllers;

use App\Entity\User;
use App\Http\Transformers\UserTransformer;
use Dingo\Api\Http\Request;
use JWTAuth;

class AuthController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/auth/register",
     *     summary="Registration process",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Registration",
     *     operationId="register",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         @SWG\Schema(
     *             @SWG\Property(property="email",type="string",default="example@yahoo.com"),
     *             @SWG\Property(property="password",type="string",default="password"),
     *             @SWG\Property(property="repassword",type="string",default="password"),
     *         )
     *     ),
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function register(Request $request)
    {
        $this->validate($request, ['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required']);

        $user = new User($request->only('name', 'email', 'password'));
        $user->save();

        return response()->json([
            'data'  => $user->transform(new UserTransformer),
            'token' => JWTAuth::fromUser($user),
        ])->setStatusCode(201);
    }

    /**
     * @SWG\Post(
     *     path="/auth/login",
     *     summary="Login process",
     *     method="post",
     *     tags={"Authentication"},
     *     description="Login process",
     *     operationId="login",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(property="email",type="string",example="info@example.com"),
     *              @SWG\Property(property="password",type="string",example="password"),
     *         )
     *      ),
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function login(Request $request)
    {
        $this->validate($request, ['email' => 'required|email', 'password' => 'required']);

        if ($token = JWTAuth::attempt($request->only('email', 'password'))) {
            $user = User::whereEmail($request->email)->first();

            return response()->json([
                'data'  => $user->transform(new UserTransformer),
                'token' => $token,
            ]);
        }

        $this->response->errorUnauthorized('Invalid credentials!');
    }

    public function refresh()
    {
        $current_token = JWTAuth::getToken();
        $token = JWTAuth::refresh($current_token);

        return response()->json(compact('token'));
    }

    /**
     * @SWG\Get(
     *     path="/auth/user",
     *     summary="Get authenticate user info",
     *     method="post",
     *     tags={"Authentication"},
     *     description="",
     *     operationId="me",
     *     produces={"application/json"},
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function me()
    {
        $user = $this->user;

        return response()->json([
            'data' => $user->transform(),
        ]);
    }
}
