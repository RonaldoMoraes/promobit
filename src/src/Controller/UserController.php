<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// use OpenApi\Annotations as SWG;

class UserController extends AbstractController
{
    private $userRepository;
    private $session;

    public function __construct(UserRepository $userRepository, SessionInterface $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    private function setSession(string $key, $val)
    {
        try {
            $this->session->set($key, $val);
        } catch (\Exception $e) {}
    }

    /**
     * @Route("/api/users", name="index_user", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userRepository->listAll();

            return new JsonResponse(['data' => $users], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Users could not be listed due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/register", name="store_user", methods={"POST"})
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data = array_intersect_key($data, array_flip(['name', 'email', 'phone', 'password']));
            
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return new JsonResponse(['message' => 'Expecting mandatory parameters'], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->userRepository->store($data)->toArray();

            return new JsonResponse(['data' => $user], Response::HTTP_CREATED);
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
            if(!$user = $this->userRepository->show($id)->toArray())
            {
                return new JsonResponse(['message' => 'User not found.'], Response::HTTP_BAD_REQUEST);
            }

            return new JsonResponse(['data' => $user], Response::HTTP_OK);
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
            $data = array_intersect_key($data, array_flip(['name', 'email', 'phone', 'password']));

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
