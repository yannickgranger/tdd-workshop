<?php

declare(strict_types=1);

namespace App\Domain\Banking;

use InvalidArgumentException;

/**
 * Exception levee lorsqu'un IBAN est invalide.
 *
 * C'est une exception metier (Domain) - elle fait partie du langage ubiquitaire.
 */
final class InvalidIbanException extends InvalidArgumentException
{
}
