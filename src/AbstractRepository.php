<?php

namespace App;

abstract class AbstractRepository
{
    /** @var \Medoo\Medoo $db */
    protected $db;

    protected $table;

    public function __construct()
    {
        if (empty($this->table)) {
            echo new \Exception(sprintf('You have to specify the table attribut in the %s class', get_called_class()));
            exit();
        }

        try {
            $medoo = Database::getInstance(getenv('DB_DATABASE'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            return;
        }
        $this->db = $medoo->getMedooDb();
    }
}
