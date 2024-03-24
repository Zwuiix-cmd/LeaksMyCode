<?php

namespace PracticeCore\Zwuiix\session;

use pocketmine\nbt\tag\CompoundTag;
use PracticeCore\Zwuiix\handler\RankHandler;
use PracticeCore\Zwuiix\PracticeCore;
use PracticeCore\Zwuiix\rank\Rank;
use PracticeCore\Zwuiix\utils\PlayersUtils;

class Info
{
    public const TAG_RANK = "practicecore.rank";
    public const TAG_KILL = "practicecore.kill";
    public const TAG_KILLSTREAK = "practicecore.killstreak";
    public const TAG_DEATH = "practicecore.death";
    public const TAG_CPS = "practicecore.cps";
    public const TAG_PRIVATE_MESSAGES = "practicecore.private_messages";
    public const TAG_PRIVATE_MESSAGES_SOUNDS = "practicecore.private_messages_sounds";
    public const TAG_SCOREBOARD = "practicecore.scoreboard";

    protected array $attachments = array();
    protected Rank $rank;
    protected int $kill = 0;
    protected int $killstreak = 0;
    protected int $death = 0;
    protected bool $cps = false;
    protected bool $privateMessages = false;
    protected bool $privateMessagesSounds = false;
    protected bool $scoreboard = false;
    protected ChatFormatter $chatFormatter;

    public function __construct(
        protected Session $session,
        CompoundTag $compoundTag
    ) {
        $this->rank = RankHandler::getInstance()->getRankByName($compoundTag->getString(self::TAG_RANK, RankHandler::getInstance()->getDefaultRank()->getName()));
        $this->kill = $compoundTag->getInt(self::TAG_KILL, 0);
        $this->killstreak = $compoundTag->getInt(self::TAG_KILLSTREAK, 0);
        $this->death = $compoundTag->getInt(self::TAG_DEATH, 0);
        $this->cps = $compoundTag->getString(self::TAG_CPS, "true") === "true";
        $this->privateMessages = $compoundTag->getString(self::TAG_PRIVATE_MESSAGES, "true") === "true";
        $this->privateMessagesSounds = $compoundTag->getString(self::TAG_PRIVATE_MESSAGES_SOUNDS, "true") === "true";
        $this->scoreboard = $compoundTag->getString(self::TAG_SCOREBOARD, "true") === "true";
        $this->chatFormatter = new ChatFormatter($this);
    }

    /**
     * @param CompoundTag $tag
     * @return CompoundTag
     */
    public function destruct(CompoundTag $tag): CompoundTag
    {
        $tag->setString(self::TAG_RANK, $this->getRank()->getName());
        $tag->setInt(self::TAG_KILL, $this->getKill());
        $tag->setInt(self::TAG_KILLSTREAK, $this->getKillStreak());
        $tag->setInt(self::TAG_DEATH, $this->getDeath());
        $tag->setString(self::TAG_CPS, ($this->hasCps() ? "true" : "false"));
        $tag->setString(self::TAG_PRIVATE_MESSAGES, ($this->hasPrivateMessages() ? "true" : "false"));
        $tag->setString(self::TAG_PRIVATE_MESSAGES_SOUNDS, ($this->hasPrivateMessagesSounds() ? "true" : "false"));
        $tag->setString(self::TAG_PRIVATE_MESSAGES_SOUNDS, ($this->hasPrivateMessagesSounds() ? "true" : "false"));
        $tag->setString(self::TAG_SCOREBOARD, ($this->hasScoreboard() ? "true" : "false"));
        return $tag;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return ChatFormatter
     */
    public function getChatFormatter(): ChatFormatter
    {
        return $this->chatFormatter;
    }

    /**
     * @return int
     */
    public function getKill(): int
    {
        return $this->kill;
    }

    /**
     * @param int $value
     * @return void
     */
    public function addKill(int $value = 1): void
    {
        $this->setKill($this->getKill() + $value);
    }

    /**
     * @param int $value
     * @return void
     */
    public function setKill(int $value): void
    {
        $this->kill = $value;
    }

    /**
     * @return void
     */
    public function resetKill(): void
    {
        $this->setKill(0);
    }

    /**
     * @return int
     */
    public function getKillStreak(): int
    {
        return $this->killstreak;
    }

    /**
     * @param int $value
     * @return void
     */
    public function addKillStreak(int $value = 1): void
    {
        $this->setKillStreak($this->getKillStreak() + $value);
    }

    /**
     * @param int $value
     * @return void
     */
    public function setKillStreak(int $value): void
    {
        $this->killstreak = $value;
    }

    /**
     * @return void
     */
    public function resetKillStreak(): void
    {
        $this->setKillStreak(0);
    }

    /**
     * @return int
     */
    public function getDeath(): int
    {
        return $this->death;
    }

    /**
     * @param int $value
     * @return void
     */
    public function addDeath(int $value = 1): void
    {
        $this->setDeath($this->getDeath() + $value);
    }

    /**
     * @param int $value
     * @return void
     */
    public function setDeath(int $value): void
    {
        $this->death = $value;
    }

    /**
     * @return void
     */
    public function resetDeath(): void
    {
        $this->setDeath(0);
    }

    /**
     * @return float|int
     */
    public function getRatio(): float|int
    {
        return $this->getDeath() == 0 ? $this->getKill() : ($this->getKill() + $this->getKillStreak()) / $this->getDeath();
    }

    /**
     * @return bool
     */
    public function hasCps(): bool
    {
        return $this->cps;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setCps(bool $value): void
    {
        $this->cps = $value;
    }

    /**
     * @return bool
     */
    public function hasPrivateMessages(): bool
    {
        return $this->privateMessages;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setPrivateMessages(bool $value): void
    {
        $this->privateMessages = $value;
    }

    /**
     * @return bool
     */
    public function hasPrivateMessagesSounds(): bool
    {
        return $this->privateMessagesSounds;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setPrivateMessagesSounds(bool $value): void
    {
        $this->privateMessagesSounds = $value;
    }

    /**
     * @return bool
     */
    public function hasScoreboard(): bool
    {
        return $this->scoreboard;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setScoreboard(bool $value): void
    {
        if($this->hasScoreboard() && !$value) $this->session->getScoreboard()->removeScoreboard($this->session);
        $this->scoreboard = $value;
    }

    /**
     * @return Rank
     */
    public function getRank(): Rank
    {
        return $this->rank;
    }

    /**
     * @param Rank $rank
     * @return void
     */
    public function setRank(Rank $rank): void
    {
        $this->rank = $rank;
        $this->update();
    }

    public function update(): void
    {
        $this->updateDisguise();
        $this->updateNameTag();
        $this->updatePermission();
    }


    public function updateNameTag(): void
    {
        $isDisguise = $this->session->isDisguise();
        [
            $name,
            $rank
        ] = [
            ($isDisguise ? $this->session->getDisguiseName() : $this->session->getName()),
            ($isDisguise ? RankHandler::getInstance()->getDefaultRank() : $this->getRank())
        ];

        $format = str_replace(["{RANK}", "{NAME}"], [$rank->getName(), $name], $rank->getNameTagFormat());
        $this->session->setNameTag($format);
    }

    /**
     * @return void
     */
    public function updatePermission(): void
    {
        $plugin = PracticeCore::getInstance()->getPlugin();
        foreach ($this->attachments as $attachment) {
            $this->session->removeAttachment($attachment);
        }
        $this->attachments = [];
        foreach ($this->getRank()->getPermissions() as $permission) {
            $this->attachments[] = $this->session->addAttachment($plugin, $permission, true);
        }
    }

    private function updateDisguise(): void
    {
        PlayersUtils::getInstance()->removeOnlinePlayer($this->getSession());
        PlayersUtils::getInstance()->addOnlinePlayer($this->getSession(), $this->getSession()->getDisguiseName());
    }
}