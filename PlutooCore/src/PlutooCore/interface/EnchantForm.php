<?php

namespace PlutooCore\interface;

use MusuiEssentials\libs\jojoe77777\FormAPI\CustomForm;
use MusuiEssentials\libs\jojoe77777\FormAPI\SimpleForm;
use MusuiEssentials\MusuiPlayer;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Sword;
use pocketmine\item\TieredTool;
use pocketmine\utils\SingletonTrait;

class EnchantForm
{
    use SingletonTrait;
    
    public function send(MusuiPlayer $player): void
    {
        $hand = $player->getInventory()->getItemInHand();
        if(!$hand instanceof Durable) {
            $player->sendMessage("§cL'item que vous avez dans vos main ne dispose aucun type d'enchantement.");
            return;
        }

        $form = new SimpleForm(function (MusuiPlayer $player, string|int $data = null) use($hand) {
            if(is_null($data)) return;
            $item = $player->getInventory()->getItemInHand();
            if(!$item->equals($hand)) {
                $player->sendMessage("§cVous avez changer d'item entre les différent menu!");
                return;
            }

            $match = match ($data) {
                "sharpness" => ["name" => "Tranchant", "price" => 750, "enchant" => VanillaEnchantments::SHARPNESS()],
                "efficiency" => ["name" => "Efficacité", "price" => 850, "enchant" => VanillaEnchantments::EFFICIENCY()],
                "protection" => ["name" => "Protection", "price" => 950, "enchant" => VanillaEnchantments::PROTECTION()],
                "unbreaking" => ["name" => "Solidité", "price" => 1000, "enchant" => VanillaEnchantments::UNBREAKING()],
                default => null
            };

            if(is_null($match)) {
                $player->sendMessage("§cImpossible de trouvé l'enchantement pour cette item.");
                return;
            }

            $this->sendWithEnchant($player, $match["name"], $match["price"], $match["enchant"]);
        });
        $form->setTitle("Enchantement");
        if($hand instanceof Sword) {
            $form->addButton("Tranchant", label: "sharpness");
        } elseif ($hand instanceof TieredTool) {
            $form->addButton("Efficacité", label: "efficiency");
        } elseif ($hand instanceof Armor) {
            $form->addButton("Protection", label: "protection");
        }

        $form->addButton("Solidité", label: "unbreaking");
        $form->sendToPlayer($player);
    }

    /**
     * @param MusuiPlayer $player
     * @param string $label
     * @param int $price
     * @param Enchantment $enchantment
     * @return void
     */
    public function sendWithEnchant(MusuiPlayer $player, string $label, int $price, Enchantment $enchantment): void
    {
        $hand = $player->getInventory()->getItemInHand();
        if(!$hand instanceof Durable) {
            $player->sendMessage("§cL'item que vous avez dans vos main ne dispose aucun type d'enchantement.");
            return;
        }

        $form = new CustomForm(function (MusuiPlayer $player, array $data = null) use($label, $price, $enchantment, $hand) {
            if(is_null($data)) return;
            $item = (clone $player->getInventory()->getItemInHand());
            if(!$item->equals($hand)) {
                $player->sendMessage("§cVous avez changer d'item entre les différent menu!");
                return;
            }
            $niveau = $data["niveau"];
            $price = $niveau * $price;

            if(!$player->hasMoney($price)) {
                $player->sendMessage("§cVous ne possédez pas suffisament d'argent!");
                return;
            }

            $player->reduceMoney($price);
            $player->getInventory()->setItemInHand($item->addEnchantment(new EnchantmentInstance($enchantment, $niveau)));
            $player->sendMessage("§5Vous avez bien enchanté votre item §9{$label} {$niveau}§5 pour §9{$price}$ §5!");
        });
        $form->setTitle("Enchantement");
        $form->addLabel("Enchantement: §9{$label}§r.\n\nPrix par niveau: §9{$price}$");
        $form->addSlider("Niveau", 1, $enchantment->getMaxLevel(), label: "niveau");
        $form->sendToPlayer($player);
    }
}
