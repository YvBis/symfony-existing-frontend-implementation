<?php

namespace App\Tests\Factory;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Room>
 *
 * @method        Room|Proxy                              create(array|callable $attributes = [])
 * @method static Room|Proxy                              createOne(array $attributes = [])
 * @method static Room|Proxy                              find(object|array|mixed $criteria)
 * @method static Room|Proxy                              findOrCreate(array $attributes)
 * @method static Room|Proxy                              first(string $sortedField = 'id')
 * @method static Room|Proxy                              last(string $sortedField = 'id')
 * @method static Room|Proxy                              random(array $attributes = [])
 * @method static Room|Proxy                              randomOrCreate(array $attributes = [])
 * @method static RoomRepository|ProxyRepositoryDecorator repository()
 * @method static Room[]|Proxy[]                          all()
 * @method static Room[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Room[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Room[]|Proxy[]                          findBy(array $attributes)
 * @method static Room[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Room[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Room&Proxy<Room> create(array|callable $attributes = [])
 * @phpstan-method static Room&Proxy<Room> createOne(array $attributes = [])
 * @phpstan-method static Room&Proxy<Room> find(object|array|mixed $criteria)
 * @phpstan-method static Room&Proxy<Room> findOrCreate(array $attributes)
 * @phpstan-method static Room&Proxy<Room> first(string $sortedField = 'id')
 * @phpstan-method static Room&Proxy<Room> last(string $sortedField = 'id')
 * @phpstan-method static Room&Proxy<Room> random(array $attributes = [])
 * @phpstan-method static Room&Proxy<Room> randomOrCreate(array $attributes = [])
 * @phpstan-method static ProxyRepositoryDecorator<Room, EntityRepository> repository()
 * @phpstan-method static list<Room&Proxy<Room>> all()
 * @phpstan-method static list<Room&Proxy<Room>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Room&Proxy<Room>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Room&Proxy<Room>> findBy(array $attributes)
 * @phpstan-method static list<Room&Proxy<Room>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Room&Proxy<Room>> randomSet(int $number, array $attributes = [])
 */
final class RoomFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Room::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'name' => self::faker()->text(255),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'version' => self::faker()->randomNumber(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Room $room): void {})
        ;
    }
}
