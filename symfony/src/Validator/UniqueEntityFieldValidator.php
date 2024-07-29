<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityFieldValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntityField) {
            throw new UnexpectedTypeException($constraint, UniqueEntityField::class);
        }
        if (null === $value || '' === $value) {
            return;
        }

        $entity = $this->em->getRepository($constraint->entity)->findOneBy([$constraint->field => $value]);
        if ($entity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ field }}', $value)
                ->setParameter('{{ entity }}', $constraint->entity)
                ->addViolation();
        }
    }
}