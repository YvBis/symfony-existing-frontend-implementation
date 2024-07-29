<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEntityField extends Constraint
{
    public string $message = 'The "{{ field }}" is already present in {{ entity }}.';
    public string $entity;
    public string $field;

    #[HasNamedArguments]
    public function __construct(string $entity, string $field, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->entity = $entity;
        $this->field = $field;
    }


}