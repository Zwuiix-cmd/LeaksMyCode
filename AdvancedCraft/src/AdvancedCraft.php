<?php

use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use Symfony\Component\Filesystem\Path;

class AdvancedCraft extends PluginBase
{
    protected Config $config;

    public function onEnable(): void
    {
        $this->saveResource("configuration.json");
        $this->config = new Config(Path::join($this->getDataFolder(), "configuration.json"), Config::JSON);
        $this->removeCrafts($this->config->get("delete", []));
        $this->registerCrafts($this->config->get("new", []));
    }

    /**
     * @param array $items
     * @return void
     */
    public function registerCrafts(array $items): void
    {
        foreach ($items as $name => $value) {
            $input = [];
            $output = $this->getItem($value["output"]["id"], $value["output"]["count"] ?? 1);
            $shape = $value["shape"];

            foreach ($value["input"] as $key => $item) {
                $input[$key] = new ExactRecipeIngredient($this->getItem($item));
            }

            Server::getInstance()->getCraftingManager()->registerShapedRecipe(new ShapedRecipe(
                $shape,
                $input,
                [$output]
            ));
        }
    }

    /**
     * @param string $item
     * @param int $count
     * @return Item
     */
    private function getItem(string $item, int $count = 1): Item
    {
        return (StringToItemParser::getInstance()->parse($item) ?? LegacyStringToItemParser::getInstance()->parse($item))->setCount($count);
    }

    /**
     * @param array $items
     * @return void
     */
    public function removeCrafts(array $items): void
    {
        $craftMgr = Server::getInstance()->getCraftingManager();
        $recipes = $craftMgr->getCraftingRecipeIndex();
        $newRecipes = [];

        $delete = $items;
        foreach ($recipes as $recipe) {
            $valid = true;
            if ($recipe instanceof ShapedRecipe || $recipe instanceof ShapelessRecipe) {
                foreach ($recipe->getResults() as $item) {
                    foreach ($delete as $value) {
                        $itemDelete = (StringToItemParser::getInstance()->parse($value) ?? LegacyStringToItemParser::getInstance()->parse($value));
                        if ($item->equals($itemDelete, false, false)) {
                            $valid = false;
                        }
                    }
                }
            }

            if ($valid) $newRecipes[] = $recipe;
        }

        $refClass = new \ReflectionClass(CraftingManager::class);
        $refProp = $refClass->getProperty("craftingRecipeIndex");
        $refProp->setAccessible(true);
        $refProp->setValue($craftMgr, $newRecipes);
    }
}