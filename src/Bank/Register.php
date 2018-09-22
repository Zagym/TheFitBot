<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\Role;
use Medoo\Medoo;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

class Register extends AbstractCommand
{
    /** @var Medoo $db */
    private $db;

    public function __construct(Message $message)
    {
        parent::__construct($message);

        try {
            $medoo = Database::getInstance(getenv('DB_DATABASE'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            return;
        }
        $this->db = $medoo->getMedooDb();

        $this->load();
    }

    protected function load()
    {
        $users = $this->getUser();

        if ($users instanceof Collection) {
            $errorMsg = null;

            /** @var User $user */
            foreach ($users as $user) {
                if ($this->isUserInDb($user)) {
                    $errorMsg .= sprintf('L\'utilisateur %s a déjà un compte.' . PHP_EOL, $user->username);
                    break;
                }
                $this->insert($user);
            }

            if ($errorMsg) {
                $this->channel->send($errorMsg);
                return;
            }
        } else {
            if ($this->isUserInDb($users)) {
                $this->channel->send('Vous avez déjà un compte.');
                return;
            }
            $this->insert($users);
        }
    }

    /**
     * @return String
     */
    protected function help() : String
    {
        return '';
        // TODO: Implement help() method.
    }

    /**
     * @return User|Collection
     */
    private function getUser()
    {
        $wordCount = explode(' ', $this->message->content);

        if (count($wordCount) == 1) {
            return $this->message->author;
        }

        if (!$this->isAdmin()) {
            $this->channel->send('Cette commande n\'existe pas. Essayez "?register"');
        }

        return $this->message->mentions->users;
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
     * @param User $user
     *
     * @return $this
     */
    private function insert(User $user)
    {
        $this->db->insert('users', [
            'discord_id' => $user->id
        ]);

        $userId = $this->db->id();

        $this->db->insert('banks', [
            'balance' => 0,
            'user_id' => $userId,
        ]);

        return $this;
    }

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

}
