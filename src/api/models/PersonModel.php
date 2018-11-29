<?php namespace Api\Models;

/**
 * GroupModel Class exists in the Api\Models namespace
 * A Model interacts with database, and return the results to a Controller
 *
 * @category Model
 */

class PersonModel extends AirCoreDB
{
    private $_table = 'Person';

    /**
     * Use this method to initialize
     * dependencies.
     * This is called from the parent AirCoreDB class
     *
     * @return void
     */
    public function onInit()
    {

    }

    /**
     * Get Data
     *
     * @param string $key
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function getPersons(string $key, int $limit = 10, int $offset = 0): ?array
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('name', 'LIKE', $key)
            ->orWhere('email', 'LIKE', $key)
            ->orderBy('name', 2)
            ->fields('t.id, t.name, t.email')
            ->limit($limit)
            ->offset($offset);
    }
    /**
     * Get All Data
     *
     * @return array
     */
    public function getAllPersons(): ?array
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->get();
    }

    /**
     * Count Persons
     *
     * @param string $key
     * @return integer
     */
    public function countPersons(string $key): int
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('name', 'LIKE', $key)
            ->orWhere('email', 'LIKE', $key)
            ->count();
    }

    // For Admin
    /**
     * Get Single Person
     *
     * @param integer $id
     * @return object
     */
    public function getPerson(int $id): ?object
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('id', $id)
            ->single();
    }

    /**
     * Update Person Info
     *
     * @param array $data
     * @param integer $id
     * @return bool
     */
    public function updatePerson(array $data, int $id): bool
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('id', $id)
            ->update($data);

    }

    /**
     * Add Person Info
     *
     * @param array $data
     * @return bool
     */
    public function addPerson(array $data): bool
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->add($data);

    }

    /**
     * Delete Person from Table
     *
     * @param integer $id
     * @return boolean
     */
    public function deletePerson(int $id): bool
    {
        return $this->airCoreDB
            ->table($this->_table)
            ->where('id', $id)
            ->delete();

    }

}
