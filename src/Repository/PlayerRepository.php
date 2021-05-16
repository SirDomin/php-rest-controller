<?php

namespace App\Repository;

use App\Entity\Player;

class PlayerRepository extends DatabaseRepository {

    public function __construct()
    {
        parent::__construct();
    }

    public function findOneById(int $id) {
        return parent::getRecordData('player', $id, 'id');
    }

    public function findOneByName(string $name) {
        return parent::getRecordData('player', $name, 'name');
    }

    public function findOneByNameAndGame(string $name, int $gameId) {
        return parent::getRecordDataMultiCondition('player', ['summonerName' => $name, 'gameId' => $gameId]);
    }

    public function save(Player $player): int {
        if ($player->id > 0) {
            return parent::updateData('player', (array) $player, 'id');
        } else {
            unset($player->id);
            return parent::insertRecordData('player', (array) $player, true);
        }
    }
}
