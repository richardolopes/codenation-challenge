<?php

// Token da app.
require_once 'config.php';

$url = "https://api.codenation.dev/v1/challenge/dev-ps/generate-data?token=$TOKEN";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

$contents = json_decode($response);
$decode = '';
$aux = 26 - $contents->numero_casas;

echo '<label>Mensagem criptografada: ';
echo "<input type=text value='$contents->cifrado' style='width: 500px'>";
echo '</label>';
echo '<br>';

for ($i = 0; $i < strlen($contents->cifrado); $i++) {
    $ord = ord(substr($contents->cifrado, $i, $i + 1));

    if ($ord >= 97 && $ord <= 122) {
        if ($ord + $aux > 122) {
            $pos = 122 - $ord;
            $decode .= chr(97 + (($aux - 1) - $pos));
        } else {
            $decode .= chr($ord + $aux);
        }
    } else {
        $decode .= chr($ord);
    }
}

echo '<label>Mensagem descriptografada: ';
echo "<input type=text value='$decode' style='width: 500px'>";
echo '</label>';
echo '<br>';

$answer = array(
    'numero_casas' => $contents->numero_casas,
    'token' => $contents->token,
    'cifrado' => $contents->cifrado,
    'decifrado' => $decode,
    'resumo_criptografico' => sha1($decode),
);

$file = fopen('answer.json', 'w');
fwrite($file, json_encode($answer));

$cfile = new CURLFile('answer.json', 'multipart/form-data', 'answer.json');

$ch = curl_init("https://api.codenation.dev/v1/challenge/dev-ps/submit-solution?token=$TOKEN");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'answer' => $cfile,
    ],
]);

$response = curl_exec($ch);
curl_close($ch);

echo '<br>';
echo $response;
echo '<br>';
