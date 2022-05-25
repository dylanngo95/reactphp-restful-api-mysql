<?php

namespace App\Controller;

use App\JsonResponse;
use App\Users;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

final class CreateUser
{
    private Users $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = json_decode((string)$request->getBody(), true);
        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';

        $this->users->create($name, $email)
            ->then(
                function () {
                   // Logger Successfully
                },
                function (Exception $error) {
                    // Logger Error
                }
            );
        return JsonResponse::ok(['data' => '', 'status' => 200]);
    }
}
