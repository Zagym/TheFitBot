<?php

namespace App\Bank;

use CharlotteDunois\Yasmin\Models\Role;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

trait BankTrait
{
    /**
     * @param User $user
     *
     * @return bool
     */
    private function isUserInDb(User $user)
    {
        return $this->db->has('users', [
            'AND' => [
                'discord_id' => $user->id
            ]
        ]);
    }

    /**
     * @return bool
     */
    private function isAdmin() : bool
    {
        $roles = $this->message->member->roles;

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->permissions->has('BAN_MEMBERS')) {
                return true;
            }
        }

        return false;
    }


    /**
     * @return User|Collection
     */
    private function getUser()
    {
        $wordCount = explode(' ', $this->message->content);

        if (count($wordCount) == 1) {
            return $this->author;
        }

        return $this->message->mentions->users;
    }

    private function getUserIdDb(User $user)
    {
        $userId = $this->db->select('users', 'id', [
            'discord_id' => $user->id
        ]);

        return $userId[0];
    }
}
