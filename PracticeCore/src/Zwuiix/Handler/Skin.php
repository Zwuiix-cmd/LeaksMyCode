<?php

namespace Zwuiix\Handler;

use GdImage;
use JsonException;
use Zwuiix\Main;
use Zwuiix\Player\User;

class Skin
{
    public static Main $plugin;
    public static function init(Main $main): void
    {
        self::$plugin=$main;
    }

    public function __construct()
    {
    }

    /**
     * @param User $player
     * @param string $stuffName
     * @return void
     * @throws JsonException
     */
    public function setSkin(User $player, string $stuffName, string $geometry): void
    {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = self::$plugin->getDataFolder()."skin/".$name.".txt";
        if(filesize($path) == 65536){
            $path = $this->imgTricky($name, $stuffName,128);
            $size = 128;
        }else{
            $size = 64;
            $path = $this->imgTricky($name, $stuffName,64);
        }

        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < $size; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~($colorat >> 24)) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r).chr($g).chr($b).chr($a);
            }
        }
        @imagedestroy($img);
        /*$player->setSkin(new \pocketmine\entity\Skin($skin->getSkinId(), $skinbytes, "", "geometry.colria", file_get_contents(self::$plugin->datafolder."geometry/{$geometry}.json")));
        $player->sendSkin();*/
    }
    public function imgTricky(string $name,string $stuffName, $size): string
    {
        $path = self::$plugin->getDataFolder();
        $locate="skin";

        $down = imagecreatefrompng($path."skin/".$name.".png");
        if($size == 128){
            if(file_exists($path.$locate."/".$stuffName."_".$size.".png")){
                $upper = imagecreatefrompng($path.$locate."/".$stuffName."_".$size.".png");
            }else{
                $upper = $this->resize_image($path.$locate."/".$stuffName.".png",128,128);
            }
        }else{
            $upper = imagecreatefrompng($path.$locate."/".$stuffName.".png");
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));

        imagealphablending($down, true);
        imagesavealpha($down, true);

        imagecopymerge($down,$upper, 0, 0, 0, 0,$size,$size,100);

        imagepng($down, $path."steve.png");
        return self::$plugin->getDataFolder()."steve.png";
    }

    public function resize_image($file, $w, $h, $crop=FALSE): GdImage|bool
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

    /**
     * @param User $player
     * @return void
     * @throws JsonException
     */
    public function resetSkin(User $player): void
    {
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = self::$plugin->getDataFolder()."skin/".$name.".png";
        $path2 = self::$plugin->getDataFolder()."skin/".$name.".txt";
        if(filesize($path2) == 65536){
            $size = 128;
        }else {
            $size = 64;
        }
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < $size; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~($colorat >> 24)) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        /*$player->setSkin(new \pocketmine\entity\Skin($skin->getSkinId(), $skinbytes, "", "geometry.colria",file_get_contents(self::$plugin->getDataFolder(). "/geometry/steve.json")));
        $player->sendSkin();*/
    }

    public function saveSkin(\pocketmine\entity\Skin $skin, $name): void
    {
        $path =  self::$plugin->getDataFolder();

        if(!file_exists($path."skin")){
            mkdir($path."skin");
        }

        if(file_exists($path."skin/".$name.".txt")){
            unlink($path."skin/".$name.".txt");
        }

        file_put_contents($path."skin/".$name.".txt",$skin->getSkinData());

        if(filesize($path."skin/".$name.".txt") == 65536){
            $img = $this->toImage($skin->getSkinData(),128,128);
        }else{
            $img = $this->toImage($skin->getSkinData(),64,64);
        }
        imagepng($img, $path."skin/".$name.".png");
    }
    public function toImage($data, $height, $width): GdImage|bool
    {
        $pixelarray = str_split(bin2hex($data), 8);
        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $position = count($pixelarray) - 1;
        while (!empty($pixelarray)){
            $x = $position % $width;
            $y = ($position - $x) / $height;
            $walkable = str_split(array_pop($pixelarray), 2);
            $color = array_map(function ($val){ return hexdec($val); }, $walkable);
            $alpha = array_pop($color);
            $alpha = ((~((int)$alpha)) & 0xff) >> 1;
            array_push($color, $alpha);
            imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
            $position--;
        }
        return $image;
    }
}