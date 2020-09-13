<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionUtil
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function set(string $key, $val)
    {
        try {
            $this->session->set($key, $val);
        } catch (\Exception $e) {}
        return true;
    }

    public function get(string $key)
    {
        try {
            $value = $this->session->get($key);
            return empty($value) ? false : $value;
        } catch (\Exception $e) {}
        return false;
    }

    public function compareValue($key, $valueToCompare = null)
    {
        try {
            $value = $this->session->get($key);
            $valueToCompare = $valueToCompare ?? $value;
            if (!!$value && $value === $valueToCompare)
            {
                return true;
            }
        } catch (\Exception $e) {}
        return false;
    }
}