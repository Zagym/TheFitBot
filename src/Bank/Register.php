<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use CharlotteDunois\Yasmin\Models\Message;
use Medoo\Medoo;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

class Register extends AbstractCommand
{
    use BankTrait;

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
            $messages = null;

            /** @var User $user */
            foreach ($users as $user) {
                if ($this->isUserInDb($user)) {
                    $messages .= sprintf('L\'utilisateur %s a déjà un compte.' . PHP_EOL, $user->username);
                    continue;
                }
                $this->insert($user);
                $messages .= sprintf('Le compte de l\'utilisateur %s a été créé.' . PHP_EOL, $user->username);
            }

            return $this->channel->send($messages);
        }

        if ($this->isUserInDb($users)) {

            return $this->channel->send('Vous avez déjà un compte.');
        }

        $this->insert($users);

        return $this->channel->send('Votre compte à été créé.');
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
            return $this->author;
        }

        if (!$this->isAdmin()) {
            $this->channel->send('Cette commande n\'existe pas. Essayez "?register"');
        }

        return $this->message->mentions->users;
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
}
