<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class checkDate extends Constraint
{
    public $message = 'The date is invalid (should be greater than today date)';
}