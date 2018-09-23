<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use App\Repository\BankRepository;
use CharlotteDunois\Yasmin\Models\Message;
use Medoo\Medoo;

class SetCommand extends AbstractCommand
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
        if (!$this->isAdmin()) {
            return $this->channel->send('Vous n\'avez pas les droits suffisant pour executer cette commande.');
        }

        if (!$this->getNumber()) {
            return $this->channel->send('Il y a une erreur dans votre commande. Essayez : '. PHP_EOL .'"?set @User [Nombre]"');
        }

        $userCollection = $this->getUser();
        $user = $userCollection->first();

        if (!$this->isUserInDb($user)) {
         return $this->channel->send(sprintf('L\'utilisateur %s n\'as pas de compte.', $user->username));
        }

        $banks = new BankRepository();
        $banks->setBalance($this->getUserIdDb($user), $this->getNumber());

        if ($user->id == $this->author->id) {
            return $this->channel->send(sprintf('Vous avez maintenant %s en banque.', $this->getNumber()));
        }

        return $this->channel->send(sprintf('L\'utilisateur %s a maintenant %s en banque', $user->username, $this->getNumber()));
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

        return $command[2];
    }
}
