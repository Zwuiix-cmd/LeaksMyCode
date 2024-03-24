<?php

namespace AdvancedPrivateChest\Zwuiix\libs\Zwuiix\AutoLoader;

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
            $server->getPluginManager()->registerEvents(new $v(), $plugin);
        }
    }

    /**
     * @param Plugin $plugin
     * @param string $path
     * @return void
     */
    public function loadCommand(Plugin $plugin, string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            Server::getInstance()->getCommandMap()->register($file, new $v());
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
            new $v();
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function loadItem(string $path = ""): void
    {
        if($path == "") {
            return;
        }

        $scan = PathScanner::scanDirectory($path, ["php"]);
        foreach ($scan as $file) {
            $v = $this->getUsePathWithPathFile($file);
            new $v();
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private function getUsePathWithPathFile(string $path): string
    {
        $split = explode("src\\", str_replace("/", "\\", str_replace(".php", "", $path)));
        return "\\" . $split[1];
    }
}