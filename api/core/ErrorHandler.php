<?php
class ErrorHandler {
    public static function handle(Throwable $e) {
        $statusCode = $e->getCode() ?: 500;
        $message = $e->getMessage();

        if (!is_int($statusCode) || $statusCode < 100 || $statusCode > 599) {
            $statusCode = 500;
        }

        if ($statusCode === 500 && !APP_DEBUG) {
            $message = "Une erreur interne est survenue";
        }

        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ]
        ];

        if (APP_DEBUG) {
            $response['error']['file'] = $e->getFile();
            $response['error']['line'] = $e->getLine();
            $response['error']['trace'] = $e->getTrace();
        }

        echo json_encode($response);
        exit;
    }
}

// Enregistrement des gestionnaires
set_exception_handler('ErrorHandler::handle');
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
