<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\UseCase\ValidatePaymentIban;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller pour la validation d'IBAN.
 *
 * Cette classe fait partie de la couche PRESENTATION.
 * Elle gere HTTP et delegue au Use Case.
 *
 * Caracteristiques :
 * - Depend de Symfony (HttpFoundation, AbstractController)
 * - Pas de logique metier
 * - Transforme HTTP -> Use Case -> HTTP
 *
 * Tests : Functional tests avec WebTestCase
 */
#[Route('/api/payment')]
final class PaymentController extends AbstractController
{
    public function __construct(
        private readonly ValidatePaymentIban $validatePaymentIban,
    ) {
    }

    /**
     * Valide un IBAN.
     *
     * POST /api/payment/validate-iban
     * Body : { "iban": "FR76 3000 6000 0112 3456 7890 189" }
     */
    #[Route('/validate-iban', methods: ['POST'])]
    public function validateIban(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $rawIban = $data['iban'] ?? '';

        // Delegation au Use Case
        $result = $this->validatePaymentIban->execute($rawIban);

        if ($result->isValid) {
            return new JsonResponse([
                'valid' => true,
                'iban' => $result->normalizedIban,
                'country' => $result->countryCode,
                'formatted' => $result->formattedIban,
            ]);
        }

        return new JsonResponse([
            'valid' => false,
            'error' => $result->errorMessage,
        ], Response::HTTP_BAD_REQUEST);
    }
}
