<?php

namespace App\Entity;

class Player implements EntityInterface {
    public int $id = 0;

    public string $summonerName = '';

    public int $summonerId = 0;

    public string $chamionName = '';

    public string $stats = '';

    public string $description = '';

    public string $position = '';

    public int $review = 0;

    public int $gameId = 0;

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
    public function getSummonerName(): string
    {
        return $this->summonerName;
    }

    /**
     * @param string $summonerName
     */
    public function setSummonerName(string $summonerName): void
    {
        $this->summonerName = $summonerName;
    }

    /**
     * @return int
     */
    public function getSummonerId(): int
    {
        return $this->summonerId;
    }

    /**
     * @param int $summonerId
     */
    public function setSummonerId(int $summonerId): void
    {
        $this->summonerId = $summonerId;
    }

    /**
     * @return string
     */
    public function getChamionName(): string
    {
        return $this->chamionName;
    }

    /**
     * @param string $chamionName
     */
    public function setChamionName(string $chamionName): void
    {
        $this->chamionName = $chamionName;
    }

    /**
     * @return string
     */
    public function getStats(): string
    {
        return $this->stats;
    }

    /**
     * @param string $stats
     */
    public function setStats(string $stats): void
    {
        $this->stats = $stats;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getReview(): int
    {
        return $this->review;
    }

    /**
     * @param int $review
     */
    public function setReview(int $review): void
    {
        $this->review = $review;
    }

    /**
     * @return int
     */
    public function getGameId(): int
    {
        return $this->gameId;
    }

    /**
     * @param int $gameId
     */
    public function setGameId(int $gameId): void
    {
        $this->gameId = $gameId;
    }


}
