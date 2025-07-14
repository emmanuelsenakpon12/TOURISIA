<?php
class ApiResponse {
    /*
     Réponse succès standardisée
      Toutes les dates 'YYYY-MM-DD' seront converties en 'jj_mm_aaaa'
     */
    public static function success($data = [], $statusCode = 200) {
        $data = self::formatDates($data);
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }


    public static function error($message, $statusCode = 400) {
        if (is_array($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ]
        ]);
        exit;
    }

    /* Formatage récursif de toutes les dates au format jj_mm_aaaa
     */
    private static function formatDates($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = self::formatDates($value);
                } elseif ($key === 'date' && self::isDateYmd($value)) {
                    $dateObj = DateTime::createFromFormat('Y-m-d', $value);
                    if ($dateObj) {
                        $data[$key] = $dateObj->format('d_m_Y');
                    }
                }
            }
        }
        return $data;
    }

    /*
     Vérifie si une valeur est une date au format YYYY-MM-DD
     */
    private static function isDateYmd($value) {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }
}
?>
