<?php

declare(strict_types=1);

namespace Swimming;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onMove(PlayerMoveEvent $event): void{
        $player = $event->getPlayer();
        if(!$player->isInsideOfWater()) return;
        if($player->getGenericFlag($player::DATA_FLAG_SWIMMING) && $player->isSprinting()) return;
        if(!$player->getGenericFlag($player::DATA_FLAG_SWIMMING) && $player->isSprinting()){
            $player->setGenericFlag($player::DATA_FLAG_SWIMMING, true);
            $eid = Entity::$entityCount++;
            $pk = new PlayerActionPacket();
            $pk->entityRuntimeId = $eid;
            $pk->putBlockPosition($player->getFloorX(), $player->getFloorY(), $player->getFloorZ());
            $pk->action = $pk::ACTION_START_SWIMMING;
            $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
        }else{
            $player->setGenericFlag($player::DATA_FLAG_SWIMMING, false);
            $eid = Entity::$entityCount++;
            $pk = new PlayerActionPacket();
            $pk->entityRuntimeId = $eid;
            $pk->putBlockPosition($player->getFloorX(), $player->getFloorY(), $player->getFloorZ());
            $pk->action = $pk::ACTION_STOP_SWIMMING;
            $this->getServer()->broadcastPacket($this->getServer()->getOnlinePlayers(), $pk);
            $player->setGenericFlag($player::DATA_FLAG_SWIMMING, false);
        }
    }
}