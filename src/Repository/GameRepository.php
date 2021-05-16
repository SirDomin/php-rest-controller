<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Summoner;

class GameRepository extends DatabaseRepository
{
    private string $tableName = 'game';

    public function __construct()
    {
        parent::__construct();
    }

    public function findOneById(int $id) {
        return parent::getRecordData($this->tableName, $id, 'id');
    }

    public function findOneByCode(string $code) {
        return parent::getRecordData($this->tableName, $code, 'code');
    }

    public function findGamesBySummoner(Summoner $summoner): array
    {
        return parent::getListDataMultiCondition($this->tableName, ['summonerId' => $summoner->getId()]);
    }

    public function save(Game $game) {
        if ($game->id > 0) {
            return parent::updateData($this->tableName, (array) $game, true);
        } else {
            unset($game->id);
            return parent::insertRecordData($this->tableName, (array) $game, true);
        }
    }

}