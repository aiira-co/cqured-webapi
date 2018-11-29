<?php
namespace Api\Models;

/**
 * AuthenticationModel Class exists in the Api\Models namespace
 * This class to Authourized Access to Controller
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class AuthenticationModel implements \Cqured\Router\CanActivate
{
    /**
     * Method Used to Auhtourize Access to Controller,
     * Method excepts a boolean return
     * Return false, to denied access or true to allow
     */
    public function canActivate(string $url):bool
    {
        // echo \json_encode(['error'=>'access denied']);
        return true;
    }
}
