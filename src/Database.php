<?php

namespace App;

use Medoo\Medoo;

class Database
{
    private static $instance;
    private static $dbName;
    private $medooDb;

    /**
     * @param String $dbName
     *
     * @return Database
     * @throws \Exception
     */
    public static function getInstance(String $dbName) : Database
    {
        if (self::$instance === null) {
            self::$instance = new self($dbName);
        } elseif (self::$dbName != getenv('DB_DATABASE')) {
            throw new \Exception('Only one database is allowed.');
        }

        return self::$instance;
    }

    /**
     * Database constructor.
     *
     * @param String $dbName
     */
    private function __construct(String $dbName)
    {
        self::$dbName = $dbName;

        $this->medooDb= new Medoo([
            'database_type' => getenv('DB_CONNECTION'),
            'database_name' => getenv('DB_DATABASE'),
            'server' => getenv('DB_HOST'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ]);
    }

    /**
     * Get the value of medooDb.
     *
     * @return Medoo
     */
    public function getMedooDb(): Medoo
    {
        return $this->medooDb;
    }

    /**
     * Set the value of medooDb.
     *
     * @param Medoo $medooDb
     *
     * @return Database
     */
    public function setMedooDb(Medoo $medooDb): Database
    {
        $this->medooDb = $medooDb;

        return $this;
    }
}
