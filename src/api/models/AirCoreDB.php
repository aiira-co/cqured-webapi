<?php
namespace Api\Models;

use Cqured\Entity\EntityModel;

/**
 * AirMediaDB Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */
class AirCoreDB
{
    protected $airCoreDB;

    /**
     * Connect to database in constructor
     */
    public function __construct()
    {
        $dsn = 'mysql:dbname=coreDB;host=127.0.0.1';
        $user = 'root';
        $password = 'glory';
        $this->airCoreDB = new EntityModel($dsn, $user, $password);
        $this->onInit();
    }

    /**
     * OnInit()
     * To be used by models that implements it
     * to initialize their dependencies
     * 
     * @return void
     */
    public function onInit()
    {
    }

    /**
     * For Debugging, this class returns the recent sql statement queried
     * as a string, just echo it.
     *
     * @return string
     */
    public function getSQL(): string
    {
        return $this->airCoreDB->sql;
    }

    /**
     * Get Last Inserted ID (After an INSERT stmt)
     *
     * @return integer
     */
    public function getLastId(): int
    {
        return $this->airCoreDB->postId;
    }
}
