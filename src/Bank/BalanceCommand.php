<?php

namespace App\Bank;

use App\AbstractCommand;
use App\Database;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\Utils\Collection;
use Medoo\Medoo;

class BalanceCommand extends AbstractCommand
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
            if (!$this->isAdmin()) {

                return $this->channel->send('Cette commande n\'existe pas. Essayez "?balance"');
            }
            $messages = null;

            /** @var User $user */
            foreach ($users as $user) {
                if (!$this->isUserInDb($user)) {
                    $messages .= sprintf('L\'utilisateur %s n\'as pas de compte' . PHP_EOL, $user->username);
                    continue;
                }
                $messages .= sprintf(
                    'L\'utilisateur %s a %s en banque.' . PHP_EOL,
                    $user->username,
                    $this->getBalance($this->author)
                );
            }

            return $this->channel->send($messages);
        }

        if (!$this->isUserInDb($this->author)) {
            return $this->channel->send('Vous n\'avez pas encore de compte. Veuillez vous enregistrer avec la commande "?register"');
        }

        return $this->channel->send(sprintf('Vous avez %s en banque.', $this->getBalance($this->author)));
    }

    protected function help()
    {
        // TODO: Implement help() method.
    }

    private function getBalance(User $user)
    {
        $balanceQuery = $this->db->select('users', [
            "[>]banks" =>  ["id" => "user_id"],
        ], [
            'balance',
        ], [
            'discord_id' => $user->id
        ]);

        return $balanceQuery[0]['balance'];
    }
}
