<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchantsShop\shops;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchantsShop\enchants\PlaceholderEnchant;
use DaPigGuy\PiggyCustomEnchantsShop\PiggyCustomEnchantsShop;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\utils\Config;

class UIShopsManager
{
    private Config $file;

    /** @var UIShop[] */
    private array $shops = [];

    public function __construct(private PiggyCustomEnchantsShop $plugin)
    {
        @mkdir($this->plugin->getDataFolder() . "ui");
        $this->file = new Config($this->plugin->getDataFolder() . "ui/shops.yml");
    }

    public function initShops(): void
    {
        foreach ($this->file->getAll() as $key => $value) {
            $this->shops[$key] = new UIShop((int)str_replace("id:", "", (string)$key), CustomEnchantManager::getEnchantmentByName($value[0]) ?? Enchantment::fromString($value[0]) ?? new PlaceholderEnchant(0, $value[0]), $value[1], $value[2]);
        }
    }

    public function addShop(UIShop $shop): void
    {
        $key = "id:" . $shop->getId();
        $this->file->setNested($key, [str_replace(" ", "_", PiggyCustomEnchantsShop::$vanillaEnchantmentNames[$shop->getEnchantment()->getName()] ?? $shop->getEnchantment()->getName()), $shop->getEnchantmentLevel(), $shop->getPrice()]);
        $this->file->save();
        $this->shops[$key] = $shop;
    }

    public function removeShop(UIShop $shop): void
    {
        $key = "id:" . $shop->getId();
        $this->file->removeNested($key);
        $this->file->save();
        if (isset($this->shops[$key])) {
            unset($this->shops[$key]);
        }
    }

    public function getShopById(int $id): ?UIShop
    {
        return isset($this->shops["id:" . $id]) ? $this->shops["id:" . $id] : null;
    }

    /**
     * @return UIShop[]
     */
    public function getShops(): array
    {
        return $this->shops;
    }

    public function getNextId(): int
    {
        return count($this->shops);
    }
}