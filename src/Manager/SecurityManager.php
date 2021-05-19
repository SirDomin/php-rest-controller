<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Request\Request;

class SecurityManager
{
    private UserRepository $userRepository;

    private Request $request;

    private ?User $user = null;

    public function __construct(UserRepository $userRepository, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function authorize(array $allow): bool
    {
        $authorizationToken = $this->request->getAuth();
        assert($authorizationToken !== null, new \Exception('unauthorized', 401));

        $authorizationToken = str_replace('Bearer: ', '', $authorizationToken);

//        try {
            /** @var User $user */
            $user = $this->userRepository->findOneBy('token', $authorizationToken);
//        }catch(\Error $exception) {
//            dd($exception->getMessage());
//            throw new \Exception('unauthorized', 401);
//        }

        assert(in_array($user->getRole(), $allow), new \Exception('unauthorized', 401));

        $this->setUser($user);

        return true;
    }
}