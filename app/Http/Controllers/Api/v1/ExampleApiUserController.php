<?php

namespace App\Http\Controllers\Api\v1;

use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Transformers\UserTransformer;
use Illuminate\Http\Request;

class ExampleApiUserController extends Controller
{

    /**
     * model
     * @var [type]
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @SWG\Get(
     *     path="/user",
     *     summary="",
     *     method="get",
     *     tags={"user"},
     *     description="",
     *     operationId="index",
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="")
     * )
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index()
    {
        return $this->response
            ->collection(
                $this->user->get(),
                new UserTransformer
            );
    }

    /**
     * @SWG\Post(
     *     path="/user",
     *     summary="",
     *     method="post",
     *     tags={"user"},
     *     description="",
     *     operationId="store",
     *     produces={"application/json"},
     *     @SWG\Response(response="200", description="")
     * )
     **/
    public function store(Request $request)
    {
        $user = $this->user->firstOrcreate($request->except('_token'));
        return $this->response->item($user, new UserTransformer);
    }

    /**
     * @SWG\Get(
     *     path="/user/{id}",
     *     summary="",
     *     method="get",
     *     tags={"user"},
     *     description="",
     *     operationId="show",
     *     produces={"application/json"},
     *     @SWG\Parameter(in="path",name="id",required=true,type="integer"),
     *     @SWG\Response(response="200", description="")
     * )
     **/
    public function show($id)
    {
        $user = $this->user->find($id);
        return $this->response->item($user, new UserTransformer);
    }

    /**
     * @SWG\Put(
     *     path="/user/{id}/update",
     *     summary="",
     *     method="put",
     *     tags={"user"},
     *     description="",
     *     operationId="update",
     *     produces={"application/json"},
     *     @SWG\Parameter(in="path",name="id",required=true,type="integer"),
     *     @SWG\Response(response="200", description="")
     * )
     **/
    public function update(Request $request, $id)
    {
        $user = $this->user->find($id);
        $user->update($request->except('_token'));
        return $this->response->item($user, new UserTransformer);
    }

    /**
     * @SWG\Delete(
     *     path="/user/{id}/delete",
     *     summary="",
     *     method="delete",
     *     tags={"user"},
     *     description="",
     *     operationId="destroy",
     *     produces={"application/json"},
     *     @SWG\Parameter(in="path",name="id",required=true,type="integer"),
     *     @SWG\Response(response="200", description="")
     * )
     **/
    public function destroy($id)
    {
        $user = $this->user->find($id);
        $category->delete();
        return $this->response->noContent();
    }
}
