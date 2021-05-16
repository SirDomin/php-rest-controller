<?php

namespace App\Entity;

class Summoner implements EntityInterface {
    public int $id = 0;

    public string $riotId = '';

    public string $name = '';

    public array $players = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getRiotId(): string
    {
        return $this->riotId;
    }

    /**
     * @param string $riotId
     */
    public function setRiotId(string $riotId): void
    {
        $this->riotId = $riotId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
