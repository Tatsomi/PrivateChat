<?php

declare(strict_types=1);

namespace Tatsomi\PrivateChat;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

use jojoe77777\FormAPI\CustomForm;

class Main extends PluginBase implements Listener
{

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "privatechat") {
            if ($sender instanceof Player) {
                $this->openPrivateChatUI($sender);
                return true;
            } else {
                $sender->sendMessage("This command can only be used in-game.");
            }
        }
        return false;
    }

    public function openPrivateChatUI(Player $player): void
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            $targetIndex = $data[0];
            $message = $data[1];
            $targetPlayerName = $this->PName()[$targetIndex];
            $targetPlayer = $this->getServer()->getPlayerExact($targetPlayerName);

            if ($targetPlayer !== null && $targetPlayer->isOnline()) {
                $targetPlayer->sendMessage("§b[Private] §e{$player->getName()}: §f$message");
                $player->sendMessage("§b[Private] §eTo $targetPlayerName: §f$message");
            } else {
                $player->sendMessage("§cPlayer $targetPlayerName is not online.");
            }
        });

        $form->setTitle("Private Chat");
        $form->addDropdown("Select a player to chat with:", $this->PName());
        $form->addInput("Enter your message:");
        $form->sendToPlayer($player);
    }

    public function PName(): array
    {
        $list = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $list[] = $player->getName();
        }
        return $list;
    }
}
