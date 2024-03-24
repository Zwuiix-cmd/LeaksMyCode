<?php

namespace AdvancedHealthTag\Zwuiix\utils;

use AdvancedHealthTag\Zwuiix\handler\HealthTagHandler;
use pocketmine\utils\SingletonTrait;

class FormatValueColor
{
    use SingletonTrait;

    /**
     * @param float|int $value
     * @return string
     */
    public function format(float|int $value): string
    {
        $v = HealthTagHandler::getInstance()->get("colors", []);
        foreach ($v as $item) {
            if(!is_array($item)) {
                continue;
            }

            foreach ($item as $calcul => $color) {
                if($calcul === "default") {
                    return "{$color}{$value}";
                }

                $res = str_replace("{&VALUE}", $value, $calcul);
                if(str_contains($res, ">")) {
                    $p = explode(">", $res);
                    if(intval($p[0]) > intval($p[1])) {
                        return "{$color}{$value}";
                    }
                }elseif (str_contains($res, "<")) {
                    $p = explode("<", $res);
                    if(intval($p[0]) < intval($p[1])) {
                        return "{$color}{$value}";
                    }
                }
            }
        }
        return $value;
    }
}