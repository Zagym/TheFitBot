<?php

namespace App\Bank;

use CharlotteDunois\Yasmin\Models\Role;
use CharlotteDunois\Yasmin\Models\User;

trait BankTrait
{
    /**
     * @param User $user
     *
     * @return bool
     */
    protected function isUserInDb(User $user)
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
}
