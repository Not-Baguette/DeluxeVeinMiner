<?php

namespace frostcheat\deluxeveinminer\command;

use frostcheat\deluxeveinminer\command\subcommands\AddBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\AddWhitelistBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\AddWorldSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\HelpSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\ReloadSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\RemoveBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\RemoveWhitelistBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\RemoveWorldSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\SetMaxBlocksSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\StatusSubCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class VeinMinerCommand extends Command {
    
    private Plugin $plugin;
    private array $subCommands = [];

    public function __construct(Plugin $plugin) {
        parent::__construct("deluxeveinminer", "DeluxeVeinMiner commands", "/deluxeveinminer <subcommand>", ["dvm", "veinminer", "veinm", "deluxevm"]);
        $this->plugin = $plugin;
        $this->setPermission("deluxeveinminer.command");
        $this->prepare();
    }
    
    public function getPlugin(): Plugin {
        return $this->plugin;
    }

    public function prepare(): void {
        $this->subCommands["addblock"] = new AddBlockSubCommand();
        $this->subCommands["addwhitelist"] = new AddWhitelistBlockSubCommand();
        $this->subCommands["addworld"] = new AddWorldSubCommand();
        $this->subCommands["reload"] = new ReloadSubCommand();
        $this->subCommands["removeblock"] = new RemoveBlockSubCommand();
        $this->subCommands["removewhitelist"] = new RemoveWhitelistBlockSubCommand();
        $this->subCommands["removeworld"] = new RemoveWorldSubCommand();
        $this->subCommands["setmaxblocks"] = new SetMaxBlocksSubCommand();
        $this->subCommands["status"] = new StatusSubCommand();
        $this->subCommands["help"] = new HelpSubCommand($this->subCommands);
    }
    
    public function getSubCommands(): array {
        return $this->subCommands;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            return false;
        }
        
        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::colorize("&cUse /$commandLabel help"));
            return false;
        }
        
        $subCommandName = strtolower(array_shift($args));
        
        if (!isset($this->subCommands[$subCommandName])) {
            $sender->sendMessage(TextFormat::colorize("&cUnknown subcommand. Use /$commandLabel help"));
            return false;
        }
        
        $subCommand = $this->subCommands[$subCommandName];
        return $subCommand->execute($this, $sender, $args);
    }
}