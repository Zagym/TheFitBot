<?php

namespace App\Repository;

use App\AbstractRepository;

class BaseRepository extends AbstractRepository
{
    protected $table;

    public function findAll()
    {
        return $this->db->select($this->table, '*');
    }
}
