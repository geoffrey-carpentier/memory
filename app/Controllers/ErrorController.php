<?php

namespace App\Controllers;

use Core\BaseController;

class ErrorController extends BaseController
{
    public function notFound(): void
    {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => '404 — Page non trouvée'
        ]);
    }
}