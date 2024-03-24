<?php

namespace MusuiAntiCheat\Zwuiix\libs\Zwuiix\AutoLoader;

use MusuiAntiCheat\Zwuiix\modules\ModuleManager;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class Loader
{
    use SingletonTrait;

    /**
     * @param Plugin $plugin
     * @param string $path
     * @return void
     */
    public function loadListeners(Plugin $plugin, string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $server = Server::getInstance();
        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            if(class_exists($v) && file_exists($file)) $server->getPluginManager()->registerEvents(new $v(), $plugin);
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function loadCommand(string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            if(class_exists($v) && file_exists($file)) Server::getInstance()->getCommandMap()->register($file, new $v());
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function loadTask(string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            if(class_exists($v) && file_exists($file)) new $v();
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function loadModules(string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            if(class_exists($v) && file_exists($file)) ModuleManager::getInstance()->register(new $v());
        }
    }

    /**
     * @param string $path
     * @return string
     */
    public function getUsePathWithPathFile(string $path): string
    {
        $split = explode("src\\", str_replace("/", "\\", str_replace(".php", "", $path)));
        return "\\" . $split[1];
    }
}