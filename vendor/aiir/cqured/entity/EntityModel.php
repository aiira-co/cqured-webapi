<?php
namespace Cqured\Entity;

use Cqured\Core\Program;
use \PDO;

/**
 * EntityModel Class exists in the Cqured\Entity namespace
 * This class handles Database Connection (PDO)
 * Database Queries with Chain methods.
 *
 * @category Entity
 */
class EntityModel
{
    private $_pdo;
    public $prefix;

    // variable returns
    protected $data;
    public $postId;
    protected $error;

    /**
     * A static variable to hold all values of the chain methods for use in
     * the _createStatement() Method
     */
    private $_statement = [];
    private $_bindParam = [];
    public $sql;

    /**
     * Connection String
     * Eg. Using EntityModel in a file
     *
     * $dsn = 'mysql:dbname=myAppDB;host=127.0.0.1';
     *
     * $user = 'root';
     *
     * $password = 'secrete';
     *
     * $this->myAppDB = new EntityModel($dsn, $user, $password);
     */
    public function __construct($dsn, $user, $password)
    {
        /* Connect to a MySQL database using driver invocation */
        // $dsn = 'mysql:dbname=testdb;host=127.0.0.1';
        // $user = 'dbuser';
        // $password = 'dbpass';

        try {
            $this->_pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            Program::reportError($e->getMessage(), 'PDO Connection Failed');
        }
    }

    /**
     * This method is used to store raw sql statement for query
     */
    public function sql($sql): self
    {
        $this->_statement = [];
        $this->_statement['sql'] = $sql;
        return $this;
    }

    /**
     * This Method is the first to be called for chaining.
     * It sets the table to query and resets all the other methods to default
     * makes use of the table_exits method to check is the table is part of the DB.
     * it has a default alias of 't'.
     *
     * SELECT t.* FROM table t
     */

    public function table(string $table): self
    {
        $this->_statement = [];

        $tables = $this->_tableExists($table);
        if ($tables) {

            // set tables
            $this->_statement['table'] = $tables['tables'];

            //set table alias
            if (!empty($tables['alias'])) {
                $this->_statement['alias'] = $tables['alias'];

                // print_r($tables['alias']);
            }
        } else {
            Program::reportError('The Table: ' . $this->prefix . $table . ' does not exists', 'Database Query Error');
        }
        return $this;
    }

    /**
     * This Method is to set the fields of the table
     *
     * SELECT 'fields' FROM ...
     *
     * @return EntityModel
     */
    public function fields(string $fields): self
    {
        if (!isset(explode('.', $fields)[1])) {
            $fieldss = '';
            for ($i = 0; $i < count($this->_statement['table']); $i++) {
                $fieldss .= $this->_fieldExists($this->_statement['table'][$i], $fields, $this->_statement['alias'][$i]);
            }

            // echo $fieldss;
            $this->_statement['field'] = trim($fieldss, ',');
        } else {
            $this->_statement['field'] = $fields;
        }

        return $this;
    }

    /**
     * This Method is to set wheres for the statement
     *
     * WhERE ...
     *
     * @return EntityModel
     */

    public function where(string $field, string $opValue, string $value = null): self
    {
        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count($this->_statement['table']); $i++) {
                    $fieldVerified = $this->_fieldExists($this->_statement['table'][$i], $field, $this->_statement['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if (is_null($opValue)) {
                Program::reportError('Please specify second arg to set WHERE: Call DB::table(\'table\')->where(\'id\',*error*)', 'Database Query Error');
            } elseif ($opValue == "=" ||
                $opValue == "!=" ||
                $opValue == "<" ||
                $opValue == ">" ||
                $opValue == "<=" ||
                $opValue == ">=" ||
                $opValue == "BETWEEN" ||
                $opValue == "IN" ||
                $opValue == "NOT IN" ||
                $opValue == "LIKE") {
                if (is_null($value)) {
                    Program::reportError('Please specify third arg to set WHERE: Call DB::table(\'table\')->where(\'id\',\'condition\',*error*)', 'Database Query Error');

                } else {
                    $this->_statement['where'] = $field . ' ' . $opValue . ' :' . str_replace('.', '_', $field);

                    $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $value;
                }
            } else {
                $this->_statement['where'] = $field . ' = :' . str_replace('.', '_', $field);
                $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $opValue;
            }
        }

        // echo $this->dbWhere;

        return $this;
    }

    /**
     * This Method is to set wheres for the statement. used after the where() is called
     *
     * WhERE ... || ..
     *
     * @return EntityModel
     */
    public function orWhere(string $field, string $opValue, string $value = null): self
    {
        //checek if where is already set
        if ($this->_statement['where'] == null) {
            Program::reportError('Call the method: where(\'id\',$id)  before calling this method "orWhere()"', 'Database Query Error');
        }

        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count($this->_statement['table']); $i++) {
                    $fieldVerified = $this->_fieldExists($this->_statement['table'][$i], $field, $this->_statement['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if (is_null($opValue)) {
                Program::reportError('Please specify third arg to set WHERE ... OR: Call DB::table(\'table\')->orWhere(\'id\',*error*)', 'Database Query Error');
            } elseif ($opValue == "=" ||
                $opValue == "!=" ||
                $opValue == "<" ||
                $opValue == ">" ||
                $opValue == "<=" ||
                $opValue == ">=" ||
                $opValue == "BETWEEN" ||
                $opValue == "IN" ||
                $opValue == "NOT IN" ||
                $opValue == "LIKE") {
                if (is_null($value)) {
                    Program::reportError('Please specify third arg to set WHERE ... OR: Call DB::table(\'table\')->orWhere(\'id\',\'condition\',*error*)', 'Database Query Error');
                } else {
                    $OrWhere = $field . ' ' . $opValue . ' :' . str_replace('.', '_', $field);
                    $this->_statement['where'] = $this->_statement['where'] . ' OR ' . $OrWhere;
                    $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $value;
                }
            } else {
                $OrWhere = $field . ' = :' . str_replace('.', '_', $field);
                $this->_statement['where'] = $this->_statement['where'] . ' OR ' . $OrWhere;
                $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $opValue;
            }
        }

        // echo $this->dbWhere;

        return $this;
    }

    /**
     * This Method is to set wheres for the statement. used after the where() is called
     *
     * WhERE ... && ..
     *
     * @return EntityModel
     */

    public function andWhere(string $field, string $opValue, string $value = null): self
    {

        //checek if where is already set
        if ($this->_statement['where'] == null) {
            Program::reportError('Call the method: where(\'id\',$id)  before calling this method "andWhere()"', 'Database Query Error');
        }
        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count($this->_statement['table']); $i++) {
                    $fieldVerified = $this->_fieldExists($this->_statement['table'][$i], $field, $this->_statement['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if (is_null($opValue)) {
                Program::reportError('Please specify second arg to set WHERE ... AND: Call DB::table(\'table\')->where(\'id\',*error*)', 'Database Query Error');
            } elseif ($opValue == "=" ||
                $opValue == "!=" ||
                $opValue == "<" ||
                $opValue == ">" ||
                $opValue == "<=" ||
                $opValue == ">=" ||
                $opValue == "BETWEEN" ||
                $opValue == "IN" ||
                $opValue == "NOT IN" ||
                $opValue == "LIKE") {
                if (is_null($value)) {
                    Program::reportError('Please specify third arg to set WHERE ... AND: Call DB::table(\'table\')->andWhere(\'id\',\'condition\',*error*)', 'Database Query Error');
                } else {
                    $AndWhere = $field . ' ' . $opValue . ' :' . str_replace('.', '_', $field);
                    $this->_statement['where'] = $this->_statement['where'] . ' AND ' . $AndWhere;
                    $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $value;
                }
            } else {
                $AndWhere = $field . ' = :' . str_replace('.', '_', $field);
                $this->_statement['where'] = $this->_statement['where'] . ' AND ' . $AndWhere;
                $this->_bindParam[':' . str_replace('.', '_', $field) . ''] = $opValue;
            }
        }

        // echo $this->dbWhere;

        return $this;
    }

    /**
     * This Method is used at the end of a chain to query the DB.
     *
     * Returns an array of objects
     *
     * @return array;
     */
    public function get(): ?array
    {
        $sql = $this->_statement['sql'] ?? $this->_createStatement();
        return $this->_query($sql);
    }

    /**
     * This Method counts the results of a query.
     * Mostly use this method to check if an item already exists & also for pagination
     *
     * SELECT COUNT(*) ...
     *
     * returns an integer
     *
     * @return int
     */
    public function count(): int
    {
        $this->_statement['field'] = 'COUNT(*)';
        $sql = $this->_createStatement();

        return json_decode(json_encode($this->_query($sql)), true)[0]['COUNT(*)'] ?? 0;
    }

    /**
     * This Method returns the Average results of a query.
     *
     * i.e SELECT AVG(*) ...
     *
     * returns an integer
     *
     * @return int
     */
    public function avg($field = '*'): ?int
    {
        $this->_statement['field'] = 'AVG(' . $field . ')';
        $sql = $this->_createStatement();

        return json_decode(json_encode($this->_query($sql)), true)[0]['AVG(' . $field . ')'];
    }

    /**
     * This Method is used to query distinct rows
     *
     * SELECT DISTINCT(*) ...
     *
     * Used at the end of a chain method. it automatically calls the get method
     */
    public function distinct(): ?array
    {
        $this->_statement['field'] = 'DISTINCT ' . $this->_statement['field'];
        return $this->get();
    }

    /**
     * This Method is to set LIMIT for the statement, taking the last ID
     *
     * LIMIT 1 ORDER BY id ACS
     *
     * Hence it will limit it to one, order by ID
     */
    public function first()
    {
        $this->_statement['limit'] = 1;
        $sql = $this->_createStatement();
        return $this->_query($sql, false);
    }

    /**
     * This Method is no different from the first() on
     *
     * LIMIT 1 ORDER BY id ASC
     *
     * returns an object
     */
    public function single()
    {
        return $this->first();
    }

    /**
     * This Method is to set LIMIT for the statement, taking the last ID
     *
     * LIMIT 1 ORDER BY id DESC
     *
     * Hence it will limit it to one, order by ID
     * returns an object
     *
     * @return Object
     */
    public function last()
    {
        $this->_statement['limit'] = 1;
        $this->OrderBy('id');
        $sql = $this->_createStatement();
        return $this->_query($sql, false);
    }

    /**
     * This Method is to set LIMIT for the statement.
     *
     * LIMIT 5
     *
     * returns EntityModel for chaining methods
     */
    public function limit(int $limit): self
    {
        $this->_statement['limit'] = $limit;
        return $this;
    }

    /**
     * This Method is used to set OFFSET for the SQL statement
     * OFFSET 5
     *
     * Returns objects. used for pagination
     */
    public function offset(int $n)
    {
        $this->_statement['offset'] = $n;
        if (!isset($this->_statement['order'])) {
            $this->orderBy('id');
        }
        return $this->get();
    }

    /**
     * This Method sets the ORDER in which the queried data should display.
     *
     * ORDER BY 'id' 'ASC'
     *
     * @return EntityModel
     */
    public function orderBy(string $field, int $order = 1): self
    {

        // check if an alias or function exists in the query
        if (!isset(explode('.', $field)[1]) || !isset(explode('(', $field)[1])) {
            for ($i = 0; $i < count($this->_statement['table']); $i++) {
                $fieldVerified = $this->_fieldExists($this->_statement['table'][$i], $field, $this->_statement['alias'][$i]);
            }
            $field = $fieldVerified;
        }

        if ($order == 1) {
            $o = 'DESC';
        } elseif ($order == 2) {
            $o = 'ASC';
        } else {
            Program::reportError('Please specify : the parameter  for the second argument <br/> 1 for DSC, 2 for ASC', 'Database Query Error');
        }

        $this->_statement['order'] = $field . ' ' . $o;
        return $this;
    }

    /**
     * This Method is to group rows in a query.
     * best used in conjanction with count to get statistically data for graphs
     *
     * @return EntityModel
     */
    public function groupBy(string $fields): self
    {
        if (!isset(explode('.', $fields)[1])) {
            for ($i = 0; $i < count($this->_statement['table']); $i++) {
                $fieldVerified = $this->_fieldExists($this->_statement['table'][$i], $fields, $this->_statement['alias'][$i]);
            }
            $this->_statement['groupBy'] = $fieldVerified;
        } else {
            $this->_statement['groupBy'] = $fields;
        }
        return $this;
    }

    // Joining Tables

    /**
     * INNER JOIN
     *
     * SELECT * FROM Users u and INNER JOIN comments c ON u.id == c.userId
     *
     * @return EntityModel
     */
    public function join(string $table, string $alias): self
    {
        if ($this->_tableExists($table)) {
            $join = [' INNER JOIN ' . $this->prefix . $table . ' ' . $alias];
            if (!isset($this->_statement['joinTables'])) {
                $this->_statement['joinTables'] = [];
            }
            $this->_statement['joinTables'] = array_merge($this->_statement['joinTables'], $join);
        } else {
            Program::reportError('The Table: ' . $table . ' does not exists', 'Database Query Error');
        }

        return $this;
    }

    /**
     * INNER JOIN
     *
     * SELECT * FROM Users u and INNER JOIN comments c ON u.id == c.userId
     *
     * @return EntityModel
     */
    public function innerJoin(string $table, string $alias): self
    {
        if ($this->_tableExists($table)) {
            $join = [' INNER JOIN ' . $this->prefix . $table . ' ' . $alias];
            if (!isset($this->_statement['joinTables'])) {
                $this->_statement['joinTables'] = [];
            }
            $this->_statement['joinTables'] = array_merge($this->_statement['joinTables'], $join);
        } else {
            Program::reportError('The Table: ' . $table . ' does not exists', 'Database Query Error');
        }

        return $this;
    }

    /**
     * FULL JOIN
     *
     * SELECT * FROM Users u and FULL JOIN comments c ON u.id == c.userId
     *
     * @return EntityModel
     */
    public function fullJoin(string $table, string $alias): self
    {
        if ($this->_tableExists($table)) {
            $join = [' FULL JOIN ' . $this->prefix . $table . ' ' . $alias];
            if (!isset($this->_statement['joinTables'])) {
                $this->_statement['joinTables'] = [];
            }
            $this->_statement['joinTables'] = array_merge($this->_statement['joinTables'], $join);
        } else {
            Program::reportError('The Table: ' . $table . ' does not exists', 'Database Query Error');
        }

        return $this;
    }

    /**
     * LEFT JOIN
     *
     * SELECT * FROM Users u and LEFT JOIN comments c ON u.id == c.userId
     *
     * @return EntityModel
     */
    public function leftJoin(string $table, string $alias): self
    {
        if ($this->_tableExists($table)) {
            if (!isset($this->_statement['joinTables'])) {
                $this->_statement['joinTables'] = [];
            }
            $join = [' LEFT JOIN ' . $this->prefix . $table . ' ' . $alias];
            $this->_statement['joinTables'] = array_merge($this->_statement['joinTables'], $join);
        } else {
            Program::reportError('The Table: ' . $table . ' does not exists', 'Database Query Error');
        }

        return $this;
    }

    /**
     * RIGHT JOIN
     *
     * SELECT * FROM Users u and RIGHT JOIN comments c ON u.id == c.userId
     *
     * @return EntityModel
     */
    public function rightJoin(string $table, string $alias): self
    {
        if ($this->_tableExists($table)) {
            if (!isset($this->_statement['joinTables'])) {
                $this->_statement['joinTables'] = [];
            }
            $join = [' RIGHT JOIN ' . $this->prefix . $table . ' ' . $alias];
            $this->_statement['joinTables'] = array_merge($this->_statement['joinTables'], $join);
        } else {
            Program::reportError('The Table: ' . $table . ' does not exists', 'Database Query Error');
        }

        return $this;
    }

    /**
     * Used after a join method to set the condition of the joint table
     *
     * JOIN 'table2' q ON t.q_id = q.id
     *
     * @return EntityModel
     */
    public function on(string $jField, string $tField): self
    {
        // check if fields exists
        $on = [' ON ' . $jField . ' = ' . $tField];
        if (!isset($this->_statement['joinOn'])) {
            $this->_statement['joinOn'] = [];
        }
        $this->_statement['joinOn'] = array_merge($this->_statement['joinOn'], $on);
        return $this;
    }

    /**
     * This Method is to ADD / INSERT row(s) of a table`
     *
     * Last to be called at the end of a chain.
     *
     * @return Boolean
     */
    public function add(array $data): bool
    {
        $fields = array_keys($data);
        $length = count($fields);

        $field = "";
        $values = "";

        for ($i = 0; $i < $length; $i++) {
            $field .= ", `" . $fields[$i] . "`";

            $values .= ", :" . $fields[$i] . "";
        }

        $field = trim($field, ',');
        $values = trim($values, ',');

        $sql = 'INSERT INTO ' . $this->prefix;
        $sql .= explode(' ', $this->_genFieldsTables('tables'))[0] . ' (';
        $sql .= $field . ') VALUES (' . $values . ')';
        // echo $sql;

        for ($i = 0; $i < $length; $i++) {
            $this->_bindParam[':' . $fields[$i] . ''] = $data[$fields[$i]];
        }

        // print_r($this->_bindParam);

        // return true;
        $this->sql = $sql;

        if ($this->_query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This Method is to update row(s) of a table`
     *
     * Last to be called at the end of a chain.
     *
     * Must be used with where() to specify the id of the table to update.
     *
     * @return Boolean
     */
    public function update(array $data): bool
    {
        $fields = array_keys($data);

        // $basket->set("filds", $fields);

        $length = count($fields);

        // $basket->set("length", $length);

        $field = "";
        $values = "";
        for ($i = 0; $i < $length; $i++) {
            $values .= ", `" . $fields[$i] . "` = :" . $fields[$i] . "";
        }

        $values = trim($values, ',');

        $sql = 'UPDATE ' . $this->prefix;
        $sql .= explode(' ', $this->_genFieldsTables('tables'))[0] . ' t SET ' . $values;

        if ($this->_statement['where'] == null) {
            Program::reportError('Please specify data to UPDATE: Call DB::table(\'table\')->where(\'id\',$id)->update($arr)', 'Database Query Error');
        }
        $sql .= ($this->_statement['where'] == null) ? '' : ' WHERE ' . $this->_statement['where'];

        $this->sql = $sql;

        // SET bindParam
        for ($i = 0; $i < $length; $i++) {
            $this->_bindParam[':' . $fields[$i] . ''] = $data[$fields[$i]];
        }
        // echo $sql;
        // print_r($this->_bindParam);

        if ($this->_query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This Method is to delete row(s) of a table`
     *
     * Last to be called at the end of a chain.
     *
     * Must be used with where() to specify the id of the table to update.
     *
     * @return Boolean
     */
    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . $this->prefix;
        $sql .= explode(' ', $this->_genFieldsTables('tables'))[0];
        if ($this->_statement['where'] == null) {
            Program::reportError('Please specify data to DELETE: Call DB::table(\'table\')->where(\'id\',$id)->delete()', 'Database Query Error');
        }

        $sql .= ($this->_statement['where'] == null) ? '' : ' WHERE ' . str_replace('t.', '', $this->_statement['where']);
        // echo $this->_statement['where'];

        // echo $sql;
        $this->sql = $sql;

        if ($this->_query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    private function _genFieldsTables(string $get): string
    {
        //table iteration
        $tables = "";
        $fields = "";
        if (isset($this->_statement['table'])) {
            for ($i = 0; $i < count($this->_statement['table']); $i++) {
                $fields .= $this->_statement['alias'][$i] . '.*,';
                $tables .= $this->_statement['table'][$i] . ' ' . $this->_statement['alias'][$i] . ',';
            }
            // echo $tables;
        }

        $fields = $this->_statement['field'] ?? trim($fields, ',');
        $tables = trim($tables, ',');
        if ($get == "fields") {
            $results = $fields;
        } else {
            $results = $tables;
        }

        return $results;
    }

    /**
     * This Method is used mostly in GET request to create or generate the
     * sql statement from the chain methods called in the model.
     *
     * The get method then assigns this return to a variable for use in the query()
     *
     * @return String
     */
    private function _createStatement(): string
    {

        // $tableAlias = isset($alias)?'':' t';
        // $sql .=' FROM '.$this->prefix.$this->_statement['table'].$tableAlias;

        $sql = 'SELECT ' . $this->_genFieldsTables('fields');

        $sql .= ' FROM ' . $this->_genFieldsTables('tables');

        // Jion iteration

        if (isset($this->_statement['joinTables'])) {
            for ($i = 0; $i < count($this->_statement['joinTables']); $i++) {
                $sql .= $this->_statement['joinTables'][$i] . ' ' . $this->_statement['joinOn'][$i];
            }
        }

        $sql .= isset($this->_statement['where']) ? ' WHERE ' . $this->_statement['where'] : '';
        $sql .= isset($this->_statement['groupBy']) ? ' GROUP BY ' . $this->_statement['groupBy'] : '';
        $sql .= isset($this->_statement['order']) ? ' ORDER BY ' . $this->_statement['order'] : '';
        $sql .= isset($this->_statement['limit']) ? ' LIMIT ' . $this->_statement['limit'] : '';
        $sql .= isset($this->_statement['offset']) ? ' OFFSET ' . $this->_statement['offset'] : '';
        $this->sql = $sql; //want to have this static
        //   echo $sql;
        return $sql;
    }

    /**
     * This Method is to makes use of the PDO prepare method to query the statement
     * (from $this->_createStatement(), $this->add(), $this->update() & $this->delete())
     *
     * When a query is executed, we check to see if the execution was a GET, POST, PULL or DELETE.
     *
     * If it was a GET, we find out if its limited to a single row to bass pdo->fetch, if Multiple
     * rows were queried, pdo->fetchAll is used
     *
     * On the other hand, if its not a get request, we simply return a boolean to denote success or failure
     *
     * A static variable $postId is altered if the request was a post. this way we can get the lastInsertId of the postId
     * to implement in other queries if neccessary
     *
     * @return Boolean|Object
     */
    private function _query(string $sql, bool $fetchAll = true)
    {
        try {
            $query = $this->_pdo->prepare($sql);

            // Use bindParam to prevent injection

            $fields = array_keys($this->_bindParam);
            $length = count($fields);

            for ($i = 0; $i < $length; $i++) {
                $query->bindParam($fields[$i], $this->_bindParam[$fields[$i]]);
            }

            //   print_r($this->_bindParam);
            //Empty bindParam;
            $this->_bindParam = [];

            if ($query->execute()) {
                // If its a SELECT statment
                if ($sql[0] == 'S') {
                    // $query->rowCount() > 1
                    return $fetchAll ? $query->fetchAll(5) : $query->fetch(5);
                } else {
                    // If its an INSERT statment
                    if ($sql[0] == 'I') {
                        $this->postId = $this->_pdo->lastInsertId();
                    }
                    return true;
                }
            } else {
                return null;
            }
        } catch (PDOExeption $e) {
            echo '[{"error":"' . $e->message() . '"}]';
        }
    }

    /**
     * Method to check if a table exist,
     * if it does, it querys with it else it takes it out of the fields
     *
     * @return Array
     */
    private function _tableExists($tables): array
    {
        //explode to see how many tables are being queried
        $tables = explode(',', trim($tables, ','));

        $length = count($tables);
        $tablesExist = [];
        $tableAlias = [];

        for ($i = 0; $i < $length; $i++) {
            // Now trim off any whitespaces
            $indexTable = trim($tables[$i], ' ');

            // Explode with DOT '.' to see if the databasename is attached to the table
            $indexTable = explode('.', $indexTable);
            if (isset($indexTable[1])) {
                $dbname = $indexTable[0] . '.';
                // $dbname='';
                $table = $indexTable[1];
                $fromDB = 'FROM ' . $indexTable[0] . ' ';
            } else {
                $dbname = "";
                $table = $indexTable[0];
                $fromDB = '';
            }

            //now see if th table already has an alias set to it, then remove it.
            $aliasTable = explode(' ', $table);
            if (isset($aliasTable[1])) {
                // alias exists
                $alias = $aliasTable[1];
                echo $alias;
                $table = $aliasTable[0];
            } else {
                if ($length == 1) {
                    $alias = "t";
                } else {
                    $alias = "t" . $i;
                }
            }

            // SHOW TABLES FROM suiteinventory LIKE 'person'
            // if (is_array($this->connections) || ($this->connections instanceof Traversable)) {
            if (false) {
                $exists = false;
                // check all the connnections untill its true;
                for ($j = 0; $j < count($this->connections); $j++) {
                    // print_r($this->connections[$j]);
                    if ($this->_checkTableField('table', $this->connections[$j], $fromDB, $table)) {
                        $exists = true;
                    }
                }

                if ($exists) {
                    $tablesExist[$i] = $dbname . $this->prefix . $table;
                    $tableAlias[$i] = $alias;
                } else {
                    Program::reportError('The Table: ' . $dbname . $table . ' does not exist in the database', 'Database Query Error');
                }
            } else {

                //Single Connection
                if ($this->_checkTableField('table', $this->_pdo, $fromDB, $table)) {
                    $tablesExist[$i] = $dbname . $this->prefix . $table;
                    $tableAlias[$i] = $alias;
                } else {
                    Program::reportError('The Table: ' . $dbname . $table . ' does not exist in the database', 'Database Query Error');
                }
            }
        }

        return ['tables' => $tablesExist, 'alias' => $tableAlias];
    }

    /**
     * Iterator Method to query for the existence of fields and tables
     *
     * @return Boolean
     */
    private function _checkTableField($type, $con, $subject, $table): bool
    {
        switch ($type) {
            case 'field':
                return $con->query("SHOW COLUMNS FROM " . $this->prefix . $table . " LIKE '" . $subject . "'") != null;
                break;

            default:
                $tableExists = $con->query("SHOW TABLES " . $subject . "LIKE '" . $this->prefix . $table . "'");
                return $tableExists && $tableExists->rowCount() == 1;
                break;
        }

        return false;
    }

    /**
     * Method to check is a field exist, if it does,
     * it querys with it else it takes it out of the fields
     *
     * @return String
     */
    private function _fieldExists($table, $field, $alias): string
    {

        // echo 'table is: '.$table.' --- fields is: '.$field.' ---- alias is: '.$alias;
        $field = explode(',', trim($field, ','));

        $length = count($field);
        $exist = "";

        for ($i = 0; $i < $length; $i++) {
            // var_dump($field);
            if ($this->_checkTableField('field', $this->_pdo, $field[$i], $table)) {
                $exist .= ',' . $alias . '.' . trim($field[$i], ' ');
                // echo $field[$i].'<br/>';
            }
        }

        return trim($exist, ',');
    }
}
