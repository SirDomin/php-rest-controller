<?php


namespace App\Entity;

class Game implements EntityInterface {
    public int $id = 0;

    public string $result = '';

    public array $players = [];

    public string $code = '';

    public ?\DateTime $dateTime;

    /**
     * @return \DateTime|null
     */
    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime|null $dateTime
     */
    public function setDateTime(?\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function __construct()
    {
        $this->dateTime = new \DateTime('now');
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

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
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param array $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }
}
