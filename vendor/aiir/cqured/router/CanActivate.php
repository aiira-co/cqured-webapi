<?php namespace Cqured\Router;

/**
 * Interface to AuthGuard-ing
 */
interface CanActivate
{
    /**
     * This method is a must to authenticate
     *
     * @param string $url
     * @return boolean
     */
    function canActivate(string $url): bool;
}
