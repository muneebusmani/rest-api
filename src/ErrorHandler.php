<?php
class ErrorHandler
{
    /**
     * This method returns  a json encoded error message
     *
     * @param Throwable $exception The exception which is being thrown
     * @return void
     */
    public static function handleException(Throwable $exception): void
    {
        http_response_code(500);
        echo json_encode([
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
