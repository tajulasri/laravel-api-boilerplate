# Laravel API Boilerplate

## Default functionality packages
- [x] Dingo API
- [x] Bouncer Roles and permission
- [x] Darkaonline swagger (Documentation)


## Installation

1. Clone repository.
`git clone https://github.com/tajulasri/laravel-api-boilerplate.git`

2. Run composer install.
`composer install`

3. Run migrations and preseed data seeding.
`php artisan migrate --seed `

4. Run `php artisan serve`

5. Request via curl `curl -X POST localhost:8000/api/auth/login --d email=info@example.com -d password=password`

## Documentations
This boilerplate is using swagger ui for API documentation and please refer swagger php though. Documentation for local can be found using this url.
`localhost:8000/api/documentation`

Please refer this package documentation https://github.com/DarkaOnLine/L5-Swagger


## Command availables
- [x] Generate transformers with attributes.
- [x] Generate CRUD API controller with models supplied. 

## Generate transformer based on models

`php artisan make:transformer ExampleUserTransformer -m User`

This command will find attributes inside current table based on those models and put into transformer.

```php

<?php

namespace App\Http\Transformers;

use App\Entity\User;
use League\Fractal\TransformerAbstract;

class ExampleUserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [

            'id'               => $user->id,
            'company_id'       => $user->company_id,
            'first_name'       => $user->first_name,
            'last_name'        => $user->last_name,
            'email'            => $user->email,
            'password'         => $user->password,
            'mobile'           => $user->mobile,
            'activated'        => $user->activated,
            'first_time_login' => $user->first_time_login,
            'login_counter'    => $user->login_counter,
            'avatar'           => $user->avatar,
            'remember_token'   => $user->remember_token,
            'created_at'       => $user->created_at,
            'updated_at'       => $user->updated_at,
        ];
    }
}

```

Eg: Generated tranformer based on model


We have models and transfor and now we need some controller with CRUD operation into it.

Generating API controller can be done using this command and dont forget to supply transformer and model into it and its will be use as default transformer in that controller.

`php artisan api:crud Products/ManageProductController -m User -t ExampleUserTransformer`

result

```php
<?php

namespace App\Http\Controllers\Api;

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
        return $this->response->item($user,new UserTransformer);
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
        return $this->response->item($user,new UserTransformer);
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
        return $this->response->item($user,new UserTransformer);
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


```


> Specify version option during generated. 

`php artisan api:crud -m User -t UserTransformer --api-version=v1`


## IMPORTANT
Dont forget to update routes in your `api.php` files.
Lastly update API documentations by using this command.
`php artisan l5-swagger:generate`

Feel free to contribute.

