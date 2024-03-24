<?php

namespace PracticeCore\Zwuiix\trait;

use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;
use PracticeCore\Zwuiix\generator\VoidGenerator;

trait GeneratorTrait
{
    /**
     * @return void
     */
    public function loadGenerator(): void
    {
        $generators = ["void" => VoidGenerator::class];
        foreach($generators as $name => $class) GeneratorManager::getInstance()->addGenerator($class, $name, fn() => null, true);

        Server::getInstance()->getWorldManager()->loadWorld("ReplayMod", true);
    }
}