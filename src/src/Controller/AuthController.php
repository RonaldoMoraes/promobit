<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;
use App\Repository\TokenRepository;
use \Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {   
        $this->session = $session;
    }

    private function setSession(string $key, $val)
    {
        try {
            $this->session->set($key, $val);
        } catch (\Exception $e) {}
    }

    /**
     * @Route("/api/login", name="login", methods={"POST"})
     */
    public function login(Request $request, UserRepository $userRepository, TokenRepository $tokenRepository, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            // if($token = $this->session->get($data['email'])['tokenKey'])
            
            $user = $userRepository->findByEmail($data['email']);            
            if (!$user || !$encoder->isPasswordValid($user, $data['password'])) {
                return new JsonResponse(['message' => 'Email or password is wrong.'], Response::HTTP_BAD_REQUEST);
            }

            $payload = [
                "user" => $user->getUsername(),
                "exp"  => (new \DateTime())->modify("+90 minutes")->getTimestamp(),
            ];
            $jwt = JWT::encode($payload, $this->getParameter('jwt_secret'), 'HS256');
            $tokenRepository->store([
                'userId' => $user->getId(),
                'email' => $user->getEmail(),
                'key' => $jwt
            ]);
            // Store no redis
            $this->setSession($data['email'], array_merge($user->toArray(), ['tokenKey' => $jwt]));

            return new JsonResponse(['data' => ['token' => $jwt]], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Could not login that user'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
