<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Role;
use Discord\Parts\Channel\Channel;
use Discord\Parts\User\User;
use Medoo\Medoo;

class Register extends AbstractCommand
{
    /** @var Message $message */
    private $message;

    /** @var Channel $channel */
    private $channel;

    /** @var Medoo $db */
    private $db;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->channel = $message->channel;

        try {
            $medoo = Database::getInstance(getenv('DB_DATABASE'));
        } catch (\Exception $e) {
            echo $e->getMessage();
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
                $this->channel->sendMessage($errorMsg);
                return;
            }
        } else {
            // Here users is a Member. So I just get the user from the member.
            /** @var User $user */
            $user = $users->getUserAttribute();

            if ($this->isUserInDb($user)) {
                $this->channel->sendMessage('Vous avez déjà un compte.');
                return;
            }
            $this->insert($user);
        }
    }

    protected function help() : String
    {
        return;
        // TODO: Implement help() method.
    }

    private function getUser()
    {
        $wordCount = explode(' ', $this->message->content);

        if (count($wordCount) == 1) {
            return $this->message->author;
        }

        if (!$this->isAdmin()) {
            $this->channel->sendMessage('Cette commande n\'existe pas. Essayez "?register"');
        }

        return $this->message->getMentionsAttribute();
    }

    /**
     * @return bool
     */
    private function isAdmin() : bool
    {
        $roles = $this->message->author->roles;

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($role->permissions->ban_members) {
                return true;
            }
        }

        return false;
    }

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

    private function isUserInDb(User $user)
    {
        return $this->db->has('users', [
            'AND' => [
                'discord_id' => $user->id
            ]
        ]);
    }

}
