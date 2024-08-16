<?php

namespace dancefarm;

use pocketmine\block\Crops;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    private int $growRadius;

    public function onEnable(): void
    {
        $this->saveResource("dancefarm.yml");
        $config = new Config($this->getDataFolder() . "dancefarm.yml", Config::YAML);
        $this->growRadius = $config->get("grow-radius", 10);

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerToggleSneak(PlayerToggleSneakEvent $event): void
    {
        $player = $event->getPlayer();

        if (!$event->isSneaking()) {
            $this->growNearbyCrops($player);
        }
    }

    private function growNearbyCrops(Player $player): void
    {
        $position = $player->getPosition();
        $world = $player->getWorld();

        for ($x = -$this->growRadius; $x <= $this->growRadius; $x++) {
            for ($z = -$this->growRadius; $z <= $this->growRadius; $z++) {
                for ($y = -1; $y <= 1; $y++) {
                    $block = $world->getBlock($position->add($x, $y, $z));

                    if ($block instanceof Crops) {
                        $age = $block->getAge(); 
                        if ($age < 7) { 
                            $block->setAge($age + 1);
                            $world->setBlock($block->getPosition(), $block);
                        }
                    }
                }
            }
        }
    }
}


