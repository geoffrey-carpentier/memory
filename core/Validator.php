<?php

namespace Core;

class Validator
{
    private array $errors = [];

    /**
     * Valide un pseudo (2-20 caractères alphanumériques + tirets/underscores)
     */
    public function validateNickname(string $nickname): self
    {
        $nickname = trim($nickname);

        if (strlen($nickname) < 2 || strlen($nickname) > 20) {
            $this->errors['nickname'] = 'Pseudo : 2-20 caractères.';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $nickname)) {
            $this->errors['nickname'] = 'Pseudo : lettres, chiffres, _ et - seulement.';
        }

        return $this;
    }

    /**
     * Valide une difficulté (3-12 paires)
     */
    public function validateDifficulty(int $difficulty): self
    {
        if (!in_array($difficulty, [3, 4, 6, 8, 10, 12], true)) {
            $this->errors['difficulty'] = 'Difficulté invalide.';
        }

        return $this;
    }

    /**
     * Valide un token CSRF
     */
    public function validateCsrf(string $token): self
    {
        if (!Security::validateCsrfToken($token)) {
            $this->errors['csrf'] = 'Erreur de sécurité.';
        }

        return $this;
    }

    /**
     * Retourne les erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check si validation réussie
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }
}
