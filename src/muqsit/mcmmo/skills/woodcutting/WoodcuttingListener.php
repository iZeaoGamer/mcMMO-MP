<?php
namespace muqsit\mcmmo\skills\woodcutting;

use muqsit\mcmmo\skills\SkillListener;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\sound\PopSound;

class WoodcuttingListener extends SkillListener{

    /** @var WoodcuttingConfig */
    private $config;

    protected function init() : void{
        $this->config = new WoodcuttingConfig();
    }

    /**
     * @param PlayerInteractEvent $event
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onPlayerInteract(PlayerInteractEvent $event) : void {
        $block = $event->getBlock();
        $item = $event->getItem();
        $player = $event->getPlayer();
        $skill = $this->plugin->getSkillManager($player)->getSkill(self::WOODCUTTING);

        if($this->config->isRightTool($item)) {
            if($this->config->isLeaf($block)) {
                $skill_level = $this->plugin->getSkillManager($player)->getSkill(self::WOODCUTTING)->getLevel();
                if($skill_level >= WoodcuttingConfig::MINIMUM_LEAFBLOWER_LEVEL){
                    $level = $block->getLevel();
                    $level->useBreakOn($block, $item, $player);
                    $level->addSound(new PopSound($block));
                }
            }

            if($this->config->isLog($block) && $skill->hasAbility()) {
                $level = $block->getLevel();
                $level->useBreakOn($block, $item, $player);
                $level->addSound(new PopSound($block));
            }
        }
    }

    /**
     * @param BlockBreakEvent
     * @priority HIGH
     * @ignoreCancelled true
     */
    public function onBlockBreak(BlockBreakEvent $event) : void {
        $player = $event->getPlayer();
        $skill = $this->plugin->getSkillManager($player)->getSkill(self::WOODCUTTING);
        $drops = $this->config->getDrops($player, $event->getItem(), $event->getBlock(), $skill->getLevel(), $skill->hasAbility(), $xpreward);
        if(!empty($drops)) {
            $event->setDrops($drops);
        }

        if(!is_null($xpreward) && $xpreward > 0) {
            $this->plugin->getSkillManager($player)->addSkillXp(self::WOODCUTTING, $xpreward);
        }
    }
}
