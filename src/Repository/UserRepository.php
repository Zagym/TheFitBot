<?php

namespace App\Repository;

use CharlotteDunois\Yasmin\Models\User;

class UserRepository extends BaseRepository
{

    protected $table = 'users';

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->db->has('users', [
            'AND' => [
                'discord_id' => $user->id
            ]
        ]);
    }

    public function getUserId(User $user)
    {

        if (!$this->hasUser($user)) {
            echo new \Exception(sprintf('The user %s is not in the db', $user->username));
            exit();
        }

        $userId = $this->db->select('users', 'id', [
            'discord_id' => $user->id
        ]);

        return $userId[0];
    }
}
