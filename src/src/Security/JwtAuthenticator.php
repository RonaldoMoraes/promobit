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
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class JwtAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $params;
    private $dm;
    private $cache;

    public function __construct(EntityManagerInterface $em, ContainerBagInterface $params, DocumentManager $dm, CacheInterface $cache)
    {
        $this->em = $em;
        $this->dm = $dm;
        $this->params = $params;
        $this->cache = $cache;
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
            // @codeCoverageIgnoreStart
        }catch (\Exception $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        try {
            $credentials = str_replace('Bearer ', '', $credentials);
            $email = $this->getEmailFromJwt($credentials);
            
            $jwtCached = $this->cache->get(urlencode($email), function (ItemInterface $item) use($email, $credentials){            
                $token = $this->dm->getRepository(Token::class)->findLatestByEmail($email);
                $jwt = $credentials === $token->getKey() ? $credentials : null;

                return $jwt;
            });

            return !!$jwtCached;
            // @codeCoverageIgnoreStart
        } catch (\Exception $exception) {
            return false;
        }
        // @codeCoverageIgnoreEnd
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'message' => 'Unauthorized access.'
            // 'message' => $_ENV['APP_ENV'] !== 'production' ? $exception->getMessage() : 'Unauthorized access.'
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