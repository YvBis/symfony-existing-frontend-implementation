<?php

namespace App\Tests\Factory;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<Message>
 *
 * @method        Message|Proxy                              create(array|callable $attributes = [])
 * @method static Message|Proxy                              createOne(array $attributes = [])
 * @method static Message|Proxy                              find(object|array|mixed $criteria)
 * @method static Message|Proxy                              findOrCreate(array $attributes)
 * @method static Message|Proxy                              first(string $sortedField = 'id')
 * @method static Message|Proxy                              last(string $sortedField = 'id')
 * @method static Message|Proxy                              random(array $attributes = [])
 * @method static Message|Proxy                              randomOrCreate(array $attributes = [])
 * @method static MessageRepository|ProxyRepositoryDecorator repository()
 * @method static Message[]|Proxy[]                          all()
 * @method static Message[]|Proxy[]                          createMany(int $number, array|callable $attributes = [])
 * @method static Message[]|Proxy[]                          createSequence(iterable|callable $sequence)
 * @method static Message[]|Proxy[]                          findBy(array $attributes)
 * @method static Message[]|Proxy[]                          randomRange(int $min, int $max, array $attributes = [])
 * @method static Message[]|Proxy[]                          randomSet(int $number, array $attributes = [])
 */
final class MessageFactory extends PersistentProxyObjectFactory
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
        return Message::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'content' => self::faker()->text(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'relation' => null, // TODO add App\Entity\Room type manually
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Message $message): void {})
        ;
    }
}
