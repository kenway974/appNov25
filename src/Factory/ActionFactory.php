<?php

namespace App\Factory;

use App\Entity\Action;
use App\Entity\Block;
use App\Entity\Need;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Action>
 *
 * @method        Action|Proxy create(array|callable $attributes = [])
 * @method static Action|Proxy createOne(array $attributes = [])
 * @method static Action|Proxy find(object|array|mixed $criteria)
 * @method static Action|Proxy findOrCreate(array $attributes)
 * @method static Action|Proxy first(string $sortedField = 'id')
 * @method static Action|Proxy last(string $sortedField = 'id')
 * @method static Action|Proxy random(array $attributes = [])
 * @method static Action|Proxy randomOrCreate(array $attributes = [])
 * @method static RepositoryProxy<Action> repository()
 * @method static Action[]|Proxy[] all()
 * @method static Action[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Action[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Action[]|Proxy[] findBy(array $attributes)
 * @method static Action[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Action[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class ActionFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(3),
            'description' => self::faker()->optional()->paragraph(),
            'intension' => self::faker()->optional()->word(),
            'isDoableNow' => self::faker()->boolean(70),
            'duration' => self::faker()->optional()->numberBetween(5, 120),
            'type' => self::faker()->optional()->randomElement(['mental', 'physical', 'social', 'creative']),
            'icon' => self::faker()->optional()->randomElement([
                'fa-solid fa-brain',
                'fa-solid fa-dumbbell',
                'fa-solid fa-heart',
                'fa-solid fa-lightbulb',
            ]),
        ];
    }

    protected function initialize(): self
    {
        // Exemple d'initialisation après création si nécessaire
        return $this
            // ->afterInstantiate(function(Action $action): void {
            //     // code après instanciation
            // })
        ;
    }

    protected static function getClass(): string
    {
        return Action::class;
    }
}
