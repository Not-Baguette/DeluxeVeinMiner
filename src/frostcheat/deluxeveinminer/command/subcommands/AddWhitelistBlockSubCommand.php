<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use frostcheat\deluxeveinminer\command\SubCommandInterface;
use frostcheat\deluxeveinminer\command\VeinMinerCommand;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\TextFormat;

class AddWhitelistBlockSubCommand implements SubCommandInterface {

    public function getName(): string {
        return "addwhitelist";
    }
    
    public function getDescription(): string {
        return "Add a block to the whitelist (blocks that can be veinmined)";
    }
    
    public function getUsage(): string {
        return "/deluxeveinminer addwhitelist <block_name>";
    }
    
    public function getPermission(): ?string {
        return "deluxeveinminer.command.add.whitelist";
    }

    public function execute(VeinMinerCommand $parent, CommandSender $sender, array $args): bool {
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(TextFormat::colorize("&cYou don't have permission to use this command."));
            return false;
        }
        
        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::colorize("&cUsage: " . $this->getUsage()));
            return false;
        }
        
        $blockName = strtolower(implode(" ", $args));
        
        // get the block from string
        $item = StringToItemParser::getInstance()->parse($blockName);
        if ($item === null) {
            $sender->sendMessage(TextFormat::colorize("&cInvalid block: $blockName"));
            return false;
        }
        
        $block = $item->getBlock();
        $actualBlockName = strtolower($block->getName());
        
        // check if its already whitelisted
        if (Loader::getInstance()->isBlockWhitelisted($actualBlockName)) {
            $sender->sendMessage(TextFormat::colorize("&cThis block is already whitelisted."));
            return false;
        }
        
        // blacklist check, warn user if yes
        if (Loader::getInstance()->isBlockBlacklisted($actualBlockName)) {
            $sender->sendMessage(TextFormat::colorize("&eWarning: This block is blacklisted and will override the whitelist!"));
        }

        Loader::getInstance()->whitelistedBlocks[] = $block;
        Loader::getInstance()->save();

        $sender->sendMessage(TextFormat::colorize("&aThe block &e$actualBlockName&a has been successfully added to the whitelist."));
        return true;
    }
}