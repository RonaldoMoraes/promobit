<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/register", name="store_user", methods={"POST"})
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return new JsonResponse(['message' => 'Expecting mandatory parameters'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userRepository->store($data);

            return new JsonResponse(['data' => $user->toArray()], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be created due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/users/{id}", name="show_user", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        try {
            if(!$user = $this->userRepository->show($id))
            {
                return new JsonResponse(['message' => 'User not found.'], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(['data' => $user->toArray()], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be found due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/users/{id}", name="update_user", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data = array_filter(['name', 'email', 'phone', 'password']);

            if(!$user = $this->userRepository->find($id))
            {
                return new JsonResponse(['message' => 'User not found.'], Response::HTTP_BAD_REQUEST);
            }
            $this->userRepository->update($user, $data);

            return new JsonResponse(['data' => []], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be updated due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        try {
            if(!$user = $this->userRepository->find($id))
            {
                return new JsonResponse(['message' => 'User not found.'], Response::HTTP_BAD_REQUEST);
            }
            $this->userRepository->delete($user);

            return new JsonResponse(['data' => []], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be deleted due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
