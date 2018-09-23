<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Repository\BankRepository;
use App\Repository\UserRepository;
use CharlotteDunois\Yasmin\Models\Message;

class AddCommand extends AbstractCommand
{
    use BankTrait;

    public function __construct(Message $message)
    {
        parent::__construct($message);

        $this->load();
    }

    protected function load()
    {
        if (!$this->isAdmin()) {
            return $this->channel->send('Vous n\'avez pas les droits suffisant pour executer cette commande.');
        }

        if (!$this->getNumber()) {
            return $this->channel->send('Il y a une erreur dans votre commande. Essayez : '. PHP_EOL .
                '```?add @User [Nombre]```Le nombre doit être au maximum de 999.');
        }

        $userCollection = $this->getUser();
        $user = $userCollection->first();
        $userRepo = new UserRepository();


        if (!$userRepo->hasUser($user)) {
         return $this->channel->send(sprintf('L\'utilisateur %s n\'as pas de compte.', $user->username));
        }

        $bankRepo = new BankRepository();
        $bankRepo->addBalance($userRepo->getUserId($user), $this->getNumber());

        if ($user->id == $this->author->id) {
            return $this->channel->send(sprintf('Vous avez maintenant %s en banque.', $bankRepo->getBalance($user)));
        }

        return $this->channel->send(sprintf('L\'utilisateur %s a maintenant %s en banque', $user->username, $bankRepo->getBalance($user)));
    }

    protected function help()
    {
        // TODO: Implement help() method.
    }

    /**
     * @return bool|int
     */
    private function getNumber()
    {
        if (count($this->getUser()) != 1) {
            return false;
        }

        $command = explode(' ', $this->message->content);

        if (count($command) < 3) {
            return false;
        }

        if (!is_numeric($command[2])) {
            return false;
        }

        if ($command[2] < 1 || $command[2] > 999) {
            return false;
        }

        return $command[2];
    }
}
