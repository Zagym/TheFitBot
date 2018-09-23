<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Repository\BankRepository;
use App\Repository\UserRepository;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;

class BalanceCommand extends AbstractCommand
{
    use BankTrait;

    public function __construct(Message $message)
    {
        parent::__construct($message);

        $this->load();
    }

    protected function load()
    {
        $userRepo = new UserRepository();
        $bankRepo = new BankRepository();
        $users = $this->getUser();

        if ($users instanceof Collection) {
            if (!$this->isAdmin()) {

                return $this->channel->send('Cette commande n\'existe pas. Essayez "?balance"');
            }
            $messages = null;

            /** @var User $user */
            foreach ($users as $user) {
                if (!$userRepo->hasUser($user)) {
                    $messages .= sprintf('L\'utilisateur %s n\'as pas de compte' . PHP_EOL, $user->username);
                    continue;
                }
                $messages .= sprintf(
                    'L\'utilisateur %s a %s en banque.' . PHP_EOL,
                    $user->username,
                    $bankRepo->getBalance($this->author)
                );
            }

            return $this->channel->send($messages);
        }

        if (!$userRepo->hasUser($this->author)) {
            return $this->channel->send('Vous n\'avez pas encore de compte. Veuillez vous enregistrer avec la commande "?register"');
        }

        return $this->channel->send(sprintf('Vous avez %s en banque.', $bankRepo->getBalance($this->author)));
    }

    protected function help()
    {
        // TODO: Implement help() method.
    }
}
