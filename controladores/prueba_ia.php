<?php
// La clave se configura en el entorno del servidor y no debe guardarse en el repositorio.
$api_key = getenv('GEMINI_API_KEY') ?: ($_SERVER['GEMINI_API_KEY'] ?? '');
if ($api_key === '') {
    http_response_code(503);
    exit('La generación con IA no está configurada en el servidor.');
}

// 2. La dirección de la IA
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . rawurlencode($api_key);
// 3. El prompt: Lo que le vamos a pedir
$prompt = "Genera 3 preguntas de opción múltiple sobre el tema: Redes VLAN. Devuelve ESTRICTAMENTE un arreglo JSON con esta estructura exacta para cada pregunta: 'pregunta', 'correcta', 'falsa1', 'falsa2', 'falsa3'. No agregues texto extra ni saludos, solo el JSON puro.";

$datos = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ]
];

// 4. Configuramos la llamada HTTP
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));

// 5. Ejecutamos y recibimos la respuesta
$respuesta = curl_exec($ch);
curl_close($ch);

// 6. Imprimimos el resultado en la pantalla
echo "<h3 style='font-family: sans-serif; color: #1565c0;'>Respuesta cruda de la IA:</h3>";
echo "<pre style='background: #f4f4f4; padding: 20px; border-radius: 10px;'>" . htmlspecialchars($respuesta) . "</pre>";
?>