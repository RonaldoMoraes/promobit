<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

// use OpenApi\Annotations as SWG;

class UserController extends AbstractController
{
    private $userRepository;
    private $cache;

    public function __construct(UserRepository $userRepository, CacheInterface $cache)
    {
        $this->userRepository = $userRepository;
        $this->cache = $cache;
    }

    /**
     * @Route("/api/users", name="index_user", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        try {
            $users = $this->userRepository->listAll();

            return new JsonResponse(['data' => $users], Response::HTTP_OK);
        // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Users could not be listed due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // @codeCoverageIgnoreEnd
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

            $this->cache->get("userId-" . $user['id'], function() use ($user){
                return $user;
            });

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

            $user = $this->cache->get("userId-" . $id, function() use ($id)
            {
                $user = $this->userRepository->show($id);
                return $user ? $user->toArray() : [];
            });

            if(empty($user))
            {
                return new JsonResponse(['message' => 'User not found.'], Response::HTTP_BAD_REQUEST);
            }

            // @codeCoverageIgnoreStart
            return new JsonResponse(['data' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be found due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // @codeCoverageIgnoreEnd
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

            $this->cache->delete("userId-" . $id);
            $this->cache->get("userId-" . $id, function() use ($user){
                return $user->toArray();
            });

            // @codeCoverageIgnoreStart
            return new JsonResponse(['data' => []], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be updated due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // @codeCoverageIgnoreEnd
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
            $this->cache->delete("userId-" . $id);
            $this->userRepository->delete($user);

            // @codeCoverageIgnoreStart
            return new JsonResponse(['data' => []], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'User could not be deleted due to an error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // @codeCoverageIgnoreEnd
    }
}
