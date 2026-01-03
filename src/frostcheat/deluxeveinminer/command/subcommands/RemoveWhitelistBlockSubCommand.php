<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use frostcheat\deluxeveinminer\command\SubCommandInterface;
use frostcheat\deluxeveinminer\command\VeinMinerCommand;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\TextFormat;

class RemoveWhitelistBlockSubCommand implements SubCommandInterface {

    public function getName(): string {
        return "removewhitelist";
    }
    
    public function getDescription(): string {
        return "Remove a block from the whitelist";
    }
    
    public function getUsage(): string {
        return "/deluxeveinminer removewhitelist <block_name>";
    }
    
    public function getPermission(): ?string {
        return "deluxeveinminer.command.remove.whitelist";
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
        
        // try to get the block from string
        $item = StringToItemParser::getInstance()->parse($blockName);
        if ($item === null) {
            $sender->sendMessage(TextFormat::colorize("&cInvalid block: $blockName"));
            return false;
        }
        
        $block = $item->getBlock();
        $actualBlockName = strtolower($block->getName());
        
        // check if it's in the whitelist
        if (!Loader::getInstance()->isBlockWhitelisted($actualBlockName)) {
            $sender->sendMessage(TextFormat::colorize("&cThis block is not in the whitelist."));
            return false;
        }

        // remove from whitelist
        $loader = Loader::getInstance();
        $found = false;
        
        foreach ($loader->whitelistedBlocks as $index => $whitelistedBlock) {
            if (strtolower($whitelistedBlock->getName()) === $actualBlockName) {
                unset($loader->whitelistedBlocks[$index]);
                $loader->whitelistedBlocks = array_values($loader->whitelistedBlocks); // Reindex array
                $found = true;
                break;
            }
        }
        
        if ($found) {
            $loader->save();
            $sender->sendMessage(TextFormat::colorize("&aThe block &e$actualBlockName&a has been successfully removed from the whitelist."));
        } else {
            $sender->sendMessage(TextFormat::colorize("&cUnexpected error: block not found in whitelist."));
        }
        
        return true;
    }
}