<?php

namespace AdvancedPrivateChest\Zwuiix\command;

use AdvancedPrivateChest\Zwuiix\libs\CortexPE\Commando\args\IntegerArgument;
use AdvancedPrivateChest\Zwuiix\libs\CortexPE\Commando\args\PlayerArgument;
use AdvancedPrivateChest\Zwuiix\libs\CortexPE\Commando\BaseCommand;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\InvMenu;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransaction;
use AdvancedPrivateChest\Zwuiix\libs\muqsit\invmenu\transaction\InvMenuTransactionResult;
use AdvancedPrivateChest\Zwuiix\Main;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PrivateChestCommand extends BaseCommand
{
    protected array $permissions;
    protected int $default;
    protected string $type;
    protected string $name;
    protected array $messages;

    public function __construct()
    {
        $plugin = Main::getInstance();
        parent::__construct($plugin, "privatechest", "Open your privates chest", ["pv"]);

        $this->type = $plugin->getConfig()->getNested("menu.type", "invmenu:hopper");
        $this->name = $plugin->getConfig()->getNested("menu.name", "PrivateChest #{&COUNT}");
        $this->messages = $plugin->getConfig()->get("messages", []);
        $this->default = $plugin->getConfig()->get("default", 1);
        $this->permissions = $plugin->getConfig()->get("permissions", array());
        arsort($this->permissions);
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new IntegerArgument("nombre"));
        $this->registerArgument(1, new PlayerArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Please execute this command via a player session.");
            return;
        }

        $count = abs($args["nombre"]);
        if($count <= 0){
            $sender->sendMessage(TextFormat::RED . $this->messages["incorrect-args"]);
            return;
        }

        if(isset($args["player"]) && $sender->hasPermission("advancedprivatechest.admin")) {
            $this->send($sender, $args["player"], $count);
            return;
        }

        $max = $this->default;
        foreach ($this->permissions as $permission => $c) {
            if($sender->hasPermission($permission) or Server::getInstance()->isOp($sender->getName())) {
                $max = $c;
                break;
            }
        }

        if(!($max >= $count)) {
            $sender->sendMessage(TextFormat::RED . str_replace("{&COUNT}", $count, $this->messages["error-permission"]));
            return;
        }

        $this->send($sender, $sender->getName(), $count);
    }

    public function send(Player $player, string $name, int $count): void
    {
        $menu = InvMenu::create($this->type);
        $menu->setName(str_replace("{&COUNT}", $count, $this->name));

        $data = Main::getInstance()->getData()->getNested(strtolower("{$name}.{$count}"), []);
        $items = array();
        for ($i = 0; $i < count($data); $i++) {
            if(!isset($data[$i])) continue;

            $value = $data[$i];
            if($value instanceof Item) {
                $items[]=$value;
                continue;
            }
            $items[]=Item::jsonDeserialize($value);
        }

        $inventory = $menu->getInventory();
        $inventory->setContents($items);

        $menu->setListener(function (InvMenuTransaction $transaction) use ($player, $name, $count): InvMenuTransactionResult{
            Main::getInstance()->getData()->setNested(strtolower("{$name}.{$count}.{$transaction->getAction()->getSlot()}"), $transaction->getIn());
            return $transaction->continue();
        });

        $server = Server::getInstance();
        $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), "key Event 1 \"" . $playerName . "\"");

        $menu->send($player);
    }
}