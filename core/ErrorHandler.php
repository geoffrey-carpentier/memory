<?php

namespace Core;

class ErrorHandler
{
    /**
     * Enregistre les erreurs non attrapées
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    public static function handleException(\Throwable $e): void
    {
        error_log('Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

        $_SESSION['flash_error'] = 'Une erreur est survenue. Réessaye plus tard.';
        header('Location: /');
        exit;
    }

    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        error_log("Error [{$severity}]: {$message} in {$file}:{$line}");
        return true;
    }
}
