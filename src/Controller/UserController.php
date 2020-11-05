<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class UserController
{
    private $entityManager;
    private $request;
    private $json;
    private $user;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getCurrentRequest();

        if (!$this->validateUser($this->request)) {
            return new JsonResponse([], 401);
        }

        $this->json = json_decode($this->request->getContent());
    }

    public function createUser()
    {
        if (count((array)$this->json) <= 0) {
            return new JsonResponse([], 400);
        }

        $user = new User();
        foreach($this->json as $item => $value) {
            $user->__set($item, $value);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([], 200);
    }

    public function viewUser()
    {
        if (is_object($this->checkIfUserExists())) {
            return $this->checkIfUserExists();
        }
        return new JsonResponse($this->user->getArrayResult(), 200);
    }

    public function showUsers()
    {
        $users = $this->entityManager->getRepository(User::class)->findAllUsers();
        return new JsonResponse($users, 200);
    }

    public function deleteUser()
    {
        if (is_object($this->checkIfUserExists())) {
            return $this->checkIfUserExists();
        }

        $this->entityManager->remove($this->user->getResult()[0]);
        $this->entityManager->flush();

        return new JsonResponse([], 200);
    }

    public function updateUser()
    {
        if (is_object($this->checkIfUserExists())) {
            return $this->checkIfUserExists();
        }

        foreach($this->json as $item => $value) {
            $this->user->getResult()[0]->__set($item, $value);
        }

        $this->entityManager->flush();

        return new JsonResponse([], 200);
    }

    public function validateUser($request)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'token' => substr($request->headers->get('Authorization'), 7)
        ]);

        if (!is_object($user)) {
            return false;
        }

        return true;
    }

    public function checkIfUserExists()
    {
        if (count((array)$this->json) <= 0 || !is_int($this->json->id)) {
            return new JsonResponse([], 400);
        }

        $user = $this->entityManager->getRepository(User::class)->findById($this->json->id);

        if (empty($user->getResult()[0])) {
            return new JsonResponse([], 404);
        }

        $this->user = $user;

        return true;
    }
}