<?php

namespace App\Resolver;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Summoner;
use App\Generator\FormGenerator;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Repository\SummonerRepository;
use App\Router;
use JetBrains\PhpStorm\ArrayShape;

class GameResolver
{

    private GameRepository $gameRepository;

    private PlayerRepository $playerRepository;

    private SummonerRepository $summonerRepository;

    private Router $router;

    public function __construct(GameRepository $gameRepository, PlayerRepository $playerRepository, SummonerRepository $summonerRepository, Router $router)
    {
        $this->gameRepository = $gameRepository;
        $this->playerRepository = $playerRepository;
        $this->summonerRepository = $summonerRepository;
        $this->router = $router;
    }

    public function resolve(array $data)
    {
        $players = [];

        $game = $this->getCurrentGame($data);

        if(!isset($data['allPlayers'])) {
            return [];
        }

        foreach ($data['allPlayers'] as $dataPlayer) {

            /** @var Player $player */
            $player = $this->playerRepository->findOneByNameAndGame($dataPlayer['summonerName'], $game->getId());

            $score = $dataPlayer['scores'];

            if(!$player) {
                $player = new Player();

                $summoner = $this->summonerRepository->findOneByName($dataPlayer['summonerName']);
                if(!$summoner) {
                    $summoner = new Summoner();
                    $summoner->setName($dataPlayer['summonerName']);
                    $summoner->setId($this->summonerRepository->save($summoner));
                }

                $player->setSummonerName($dataPlayer['summonerName']);
                $player->setChamionName($dataPlayer['championName']);
                $player->setPosition($dataPlayer['position']);
                $player->setSummonerId($summoner->getId());
                $player->setGameId($game->getId());

                $player->setId($this->playerRepository->save($player));
            }

            $player->setStats(sprintf('%d/%d/%d', $score['kills'], $score['deaths'], $score['assists']));
            $this->playerRepository->save($player);

            $players[] = $this->summonerRepository->findOneById($player->getSummonerId());
        }

        return $players;
    }

    public function finalizeGame(array $data): array
    {
        $game = $this->getCurrentGame($data);

        $form = [
            'action' => $this->router->getUrlByName('save_players'),
            'data' => [],
            'name' => 'reviewSubmit'
        ];

        /** @var Player $player */
        foreach ($game->getPlayers() as $player) {
            $form['data'][] = [
                'id' => $player->getId(),
                'review' => 5,
                'description' => '',
                'championName' => $player->getChamionName()
            ];
        }

        return $form;
    }

    private function getCurrentGame(array $data): Game
    {
        $id = '';

        $arrayOfKeys = [];

        if(!isset($data['allPlayers'])) {
            return new Game();
        }

        foreach ($data['allPlayers'] as $player) {
            $arrayOfKeys[] = $player['summonerName'] . $player['championName'];
        }

        sort($arrayOfKeys);

        $id = implode('', $arrayOfKeys);

        $code = date('Ymd'). '-' .md5($id);

        $game = $this->gameRepository->findOneByCode($code);
        if (!$game) {
            $game = new Game();

            $game->setCode($code);
            $game->setId($this->gameRepository->save($game));
        }

        return $game;
    }
}