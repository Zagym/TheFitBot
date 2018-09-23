<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Repository\BankRepository;
use App\Repository\UserRepository;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

class RegisterCommand extends AbstractCommand
{
    use BankTrait;

    public function __construct(Message $message)
    {
        parent::__construct($message);

        $this->load();
    }

    protected function load()
    {
        $bankRepo = new BankRepository();
        $userRepo = new UserRepository();
        $users = $this->getUser();

        if ($users instanceof Collection) {
            if (!$this->isAdmin()) {

               return $this->channel->send('Cette commande n\'existe pas. Essayez "?register"');
            }
            $messages = null;

            /** @var User $user */
            foreach ($users as $user) {
                if ($userRepo->hasUser($user)) {
                    $messages .= sprintf('L\'utilisateur %s a déjà un compte.' . PHP_EOL, $user->username);
                    continue;
                }
                $bankRepo->insert($user);
                $messages .= sprintf('Le compte de l\'utilisateur %s a été créé.' . PHP_EOL, $user->username);
            }

            return $this->channel->send($messages);
        }

        if ($userRepo->hasUser($users)) {

            return $this->channel->send('Vous avez déjà un compte.');
        }

        $bankRepo->insert($users);

        return $this->channel->send('Votre compte à été créé.');
    }

    protected function help()
    {
        // TODO: Implement help() method.
    }
}
