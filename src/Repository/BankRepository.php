<?php

namespace App\Repository;

use CharlotteDunois\Yasmin\Models\User;

class BankRepository extends BaseRepository
{
    protected $table = 'banks';

    /**
     * @param User $user
     *
     * @return $this
     */
    public function insert(User $user)
    {
        $this->db->insert('users', [
            'discord_id' => $user->id,
            'username' => $user->username,
        ]);

        $userId = $this->db->id();

        $this->db->insert('banks', [
            'balance' => 0,
            'user_id' => $userId,
        ]);

        return $this;
    }

    public function getBalance(User $user)
    {
        $balanceQuery = $this->db->select('users', [
            "[>]banks" =>  ["id" => "user_id"],
        ], [
            'balance',
        ], [
            'discord_id' => $user->id
        ]);

        return $balanceQuery[0]['balance'];
    }

    public function setBalance($userId, $number)
    {
        $this->db->update('banks', [
            'balance' => $number
        ], [
            'user_id' => $userId
        ]);

        return $this;
    }

    public function addBalance($userId, $number)
    {
        $this->db->update('banks', [
            'balance[+]' => $number
        ], [
            'user_id' => $userId
        ]);

        return $this;
    }
}
