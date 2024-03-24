<?php

namespace Zwuiix\Utils;

use pocketmine\utils\SingletonTrait;

class CooldownFormat
{
    use SingletonTrait;

    public function getFormatBySecond(int $sec): string
    {
        $timeRestant = $sec;
        $annee = intval(abs($timeRestant / 31536000));
        $timeRestant = $timeRestant - ($annee * 31536000);
        $mois = intval(abs($timeRestant / 2635200));
        $timeRestant = $timeRestant - ($mois * 2635200);
        $jours = intval(abs($timeRestant / 86400));
        $timeRestant = $timeRestant - ($jours * 86400);
        $heures = intval(abs($timeRestant / 3600));
        $timeRestant = $timeRestant - ($heures * 3600);
        $minutes = intval(abs($timeRestant / 60));
        $secondes = intval(abs($timeRestant - $minutes * 60));

        $all=[];
        $all[]=["time" => $annee, "string" => "année(s)"];
        $all[]=["time" => $mois, "string" => "mois"];
        $all[]=["time" => $jours, "string" => "jour(s)"];
        $all[]=["time" => $heures, "string" => "heure(s)"];
        $all[]=["time" => $minutes, "string" => "minute(s)"];
        $all[]=["time" => $secondes, "string" => "seconde(s)"];

        $msg = [];
        foreach ($all as $item => $info){
            $time=$info["time"];
            $string=$info["string"];
            if($time == 0) continue;
            $msg[] = $time . " {$string}";
        }

        return implode("§7,§e ", $msg);
    }

    /**
     * @param string $format
     * @return float|int
     */
    public function getSecondByFormat(string $format): float|int
    {
        $val = substr($format, -1);
        if ($val == "a") {
            $temp = time() + (intval($format) * 31536000);
        } else if ($val == "M") {
            $temp = time() + (intval($format) * 2635200);
        } else if ($val == "S") {
            $temp = time() + (intval($format) * 604800);
        } else if ($val == "j") {
            $temp = time() + (intval($format) * 86400);
        } else if ($val == "h") {
            $temp = time() + (intval($format) * 3600);
        } else if ($val == "m") {
            $temp = time() + (intval($format) * 60);
        } else if ($val == "s") {
            $temp = time() + (intval($format));
        } else $temp = time() + 1;
        return $temp;
    }
}