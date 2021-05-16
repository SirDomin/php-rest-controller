<?php

declare(strict_types=1);


namespace App\Repository;

use App\Entity\Summoner;

final class SummonerRepository extends DatabaseRepository
{
    public function findOneByName(string $name) {
        return parent::getRecordData('summoner', $name, 'name');
    }

    public function findOneById(int $id) {
        return parent::getRecordData('summoner', $id, 'id');
    }

    public function save(Summoner $summoner) {
        if ($summoner->id > 0) {
            return parent::updateData('summoner', (array) $summoner, true);
        } else {
            unset($summoner->id);
            return parent::insertRecordData('summoner', (array) $summoner, true);
        }
    }
}
