<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use App\Repository\BankRepository;
use CharlotteDunois\Yasmin\Models\Message;
use Medoo\Medoo;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

class RegisterCommand extends AbstractCommand
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
        $banks = new BankRepository();
        $users = $this->getUser();

        if ($users instanceof Collection) {
            if (!$this->isAdmin()) {

               return $this->channel->send('Cette commande n\'existe pas. Essayez "?register"');
            }
            $messages = null;

            /** @var User $user */
            foreach ($users as $user) {
                if ($this->isUserInDb($user)) {
                    $messages .= sprintf('L\'utilisateur %s a déjà un compte.' . PHP_EOL, $user->username);
                    continue;
                }
                $banks->insert($user);
                $messages .= sprintf('Le compte de l\'utilisateur %s a été créé.' . PHP_EOL, $user->username);
            }

            return $this->channel->send($messages);
        }

        if ($this->isUserInDb($users)) {

            return $this->channel->send('Vous avez déjà un compte.');
        }

        $banks->insert($users);

        return $this->channel->send('Votre compte à été créé.');
    }

    protected function help()
    {
        // TODO: Implement help() method.
    }
}
