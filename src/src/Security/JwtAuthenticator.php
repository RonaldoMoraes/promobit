<?php

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Firebase\JWT\JWT;
use App\Entity\User;
use App\Document\Token;
use App\Repository\TokenRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $params;
    private $dm;

    public function __construct(EntityManagerInterface $em, ContainerBagInterface $params, DocumentManager $dm)
    {
        $this->em = $em;
        $this->dm = $dm;
        $this->params = $params;
    }

    private function getEmailFromJwt(string &$credentials)
    {
        $credentials = str_replace('Bearer ', '', $credentials);
        $jwt = (array) JWT::decode(
            $credentials, 
            $this->params->get('jwt_secret'),
            ['HS256']
        );
        
        return $jwt['user'];
    }

    public function start(Request $request, AuthenticationException $authException = null)
    { 
        $data = [ 
            'message' => 'Authentication Required'
        ];
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        return $request->headers->has('Authorization');
    }

    public function getCredentials(Request $request)
    {
        return $request->headers->get('Authorization');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $credentials = str_replace('Bearer ', '', $credentials);
            $email = $this->getEmailFromJwt($credentials);
            $user = $this->em->getRepository(User::class)->findByEmail($email);

            return $user;
        }catch (\Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        try {
            $credentials = str_replace('Bearer ', '', $credentials);
            $email = $this->getEmailFromJwt($credentials);
            $token = $this->dm->getRepository(Token::class)->findLatestByEmail($email);

            return $credentials === $token->getKey();
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => $_ENV['APP_ENV'] !== 'production' ? $exception->getMessage() : 'Unauthorized access.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}