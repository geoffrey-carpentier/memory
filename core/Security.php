<?php

namespace Core;

class Security
{
    /**
     * Génère un token CSRF et le stocke en session.
     * À appeler une fois au chargement du formulaire.
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valide le token CSRF reçu en POST.
     */
    public static function validateCsrfToken(string $token): bool
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Échappe les caractères HTML (alias court pour htmlspecialchars).
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
