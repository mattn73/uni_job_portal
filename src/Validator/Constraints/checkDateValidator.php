<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class checkDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof checkDate) {
            throw new UnexpectedTypeException($constraint, checkDate::class);
        }

        $dateNow = new \DateTime();

        if ($value <= $dateNow) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}