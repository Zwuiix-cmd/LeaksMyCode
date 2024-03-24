<?php

namespace Zwuiix\Handler;

use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\CraftingManagerFromDataHelper;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use Webmozart\PathUtil\Path;
use Zwuiix\Main;
use const pocketmine\RESOURCE_PATH;

class Craft
{
    use SingletonTrait;
    public static array $deletedCraft = array();
    public CraftingManager $newCratingManger;

    /**
     * @return void
     */
    public function registerCrafts(): void
    {
        foreach (Main::getInstance()->getCraftConfig()->getAll() as $name => $values) {
            $recipe = new ShapedRecipe(
                array("abc", "def", "ghi"),
                array(
                    "a" => self::getItem($values['shape'][0][0]),
                    "b" => self::getItem($values['shape'][0][1]),
                    "c" => self::getItem($values['shape'][0][2]),
                    "d" => self::getItem($values['shape'][1][0]),
                    "e" => self::getItem($values['shape'][1][1]),
                    "f" => self::getItem($values['shape'][1][2]),
                    "g" => self::getItem($values['shape'][2][0]),
                    "h" => self::getItem($values['shape'][2][1]),
                    "i" => self::getItem($values['shape'][2][2]),
                ),
                [$this->getItem($values['result'][0])]
            );
            Main::getInstance()->getServer()->getLogger()->notice("[Craft] : Adding new craft $name");
            Main::getInstance()->server->getCraftingManager()->registerShapedRecipe($recipe);
        }
    }

    /**
     * @param $item
     * @return Item
     */
    private function getItem($item): Item
    {
        $items = explode(":", $item);
        $id = intval($items[0]);
        $meta = intval($items[1]);
        if(array_key_exists(2,$items)) {
            $count = intval($items[2]);
            return ItemFactory::getInstance()->get($id, $meta, $count);
        }
        return ItemFactory::getInstance()->get($id, $meta);
    }

    public function delCraft(): void
    {
        $originalRecipePath = Path::join(RESOURCE_PATH, "legacy_recipes.json");
        $recipes = json_decode(file_get_contents($originalRecipePath), true);
        $config = new Config(Main::getInstance()->datafolder."config/delcraft.yml", COnfig::YAML);
        $deleteCraft = $config->get('deleteCraft');
        foreach ($recipes['shaped'] as $index => $recipe) {
            $id = $recipe['output'][0]['id'];
            if (!in_array($id, $deleteCraft)) continue;
            unset($recipes['shaped'][$index]);
            self::$deletedCraft[] = [
                'output' => $id,
                'input' => $recipe['input'],
                'shape' => $recipe['shape']
            ];
        }
        foreach ($recipes['shapeless'] as $index => $recipe) {
            $id = $recipe['output'][0]['id'];
            if (!in_array($id, $deleteCraft)) continue;
            unset($recipes['shapeless'][$index]);
            self::$deletedCraft[] = [
                'output' => $id,
                'input' => $recipe['input'][0]['id'],
                'shape' => null
            ];
        }
        $filePath = Main::getInstance()->datafolder . 'recipes_cache.json';
        file_put_contents($filePath, json_encode($recipes));
        $this->newCratingManger = CraftingManagerFromDataHelper::make(Path::join(Main::getInstance()->datafolder . 'recipes_cache.json'));
        $craftingManager = Server::getInstance()->getCraftingManager();
        foreach ($this->newCratingManger->getShapedRecipes() as $shapedRecipes) {
            foreach ($shapedRecipes as $recipe) {
                $craftingManager->registerShapedRecipe($recipe);
            }
        }
        @unlink($filePath);

        foreach ($deleteCraft as $crafts){
            Main::getInstance()->getServer()->getLogger()->notice("[Craft] : Removing craft $crafts");
        }
    }
}