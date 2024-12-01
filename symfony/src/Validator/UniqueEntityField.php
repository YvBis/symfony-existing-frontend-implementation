<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueEntityField extends Constraint
{
    public string $message = 'The "{{ field }}" is already present in {{ entity }}.';
    /** @var class-string */
    public string $entity;
    public string $field;

    /**
     * @param class-string<object> $entity
     */
    #[HasNamedArguments]
    public function __construct(string $entity, string $field, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->entity = $entity;
        $this->field = $field;
    }
}
