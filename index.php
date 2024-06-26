<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubago comparer</title>
    <link rel="stylesheet" href="./styles.css">
</head>

<body class="body ">


    <?php

    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }


    function getMonthlyListeners($artistName, $accessToken)
    {

        $apiUrl = "https://api.spotify.com/v1/search?q=" . urlencode($artistName) . "&type=artist";


        $headers = array(
            "Authorization: Bearer $accessToken",
        );


        $context = stream_context_create([
            'http' => [
                'header' => $headers
            ]
        ]);
        $response = file_get_contents($apiUrl, false, $context);

        $data = json_decode($response, true);
        if (isset($data['artists']['items'][0]['followers']['total'])) {
            return [
                'listeners' => $data['artists']['items'][0]['followers']['total'],
                'image' => $data['artists']['items'][0]['images'][0]['url'] ?? ''
            ];
        } else {
            return false;
        }
    }


    function getRandomSinger()
    {

        if (!isset($_SESSION['remainingSingers']) || empty($_SESSION['remainingSingers'])) {

            $singers = array(
                "Pablo Alborán",
                "David Bisbal",
                "Rozalén",
                "Aitana",
                "Manuel Carrasco",
                "Pablo López",
                "Joan Manuel Serrat",
                "Melendi",
                "Lori Meyers",
                "Vanessa Martín",
                "Estopa",
                "Rosalía",
                "Antonio Orozco",
                "Carolina Durante",
                "Ana Guerra",
                "La Oreja de Van Gogh",
                "Vanesa Martín",
                "Joaquín Sabina",
                "Quevedo",
                "Wos",
                "Pereza",
                "Malú",
                "Mecano",
                "Queen",
                "Lola Flores",
                "Ockami",
                "Rozalén",
                "Belén Aguilera",
                "Doja Cat",
                "Tokischa",
                "Karol G",
                "Becky G",
                "J Balvin",
                "Olivia Rodrigo",
                "Twenty One Pilots",
                "Valeria Castro",
                "Jani Joplin",
                "Chica sobresalto",
                "Justin Bieber",
                "Adele",
                "Julieta Venegas",
                "Camela",
                "Beyoncé",
                "Ed Sheeran",
                "Rihanna",
                "Love of Lesbian",
                "Rigoberta Bandini",
                "Katy Perry",
                "El Canto del Loco",
                "Lady Gaga",
                "Alejandro Sanz",
                "Ariana Grande",
                "Aitana",
                "Lola Indigo",
                "Nathy Peluso",
                "Bad Gyal",
                "Ana Mena",
                "Mónica Naranjo",
                "Rocio Jurado",
                "Tini",
                "Dua Lipa",
                "Natalia Lacunza",
                "Morat",
                "Eminem",
                "Amaral",
                "Dr. Dre",
                "Wolfgang Amadeus Mozart",
                "Johann Sebastian Bach",
                "Antonio Vivaldi",
                "Franz Schubert",
                "Joseph Haydn",
                "Fréderic Chopin",
                "Snoop Dogg",
                "Kase.O",
                "Lola Indigo",
                "Fondo Flamenco",
                "Vetusta Morla",
                "Iván Ferreiro",
                "Los Chikos del Maíz",
                "Rosana",
                "Nacha Pop",
                "Izal",
                "Mäbu",
                "María Isabel",
                "Second",
                "Dover",
                "Pignoise",
                "Ketama",
                "Zahara",
                "La Casa Azul",
                "La Bien Querida",
                "Los Secretos",
                "La Pegatina",
                "La Mala María",
                "Fuel Fandango",
                "El Kanka",
                "Rayden",
                "Nach",
                "SFDK",
                "La Mari de Chambao",
                "Ska-P",
                "El Barrio",
                "Wisin",
                "Yandel",
                "Jowell& Randy ",
                "Zion  Lennox ",
                "Nick Jam ",
                "DonOmar ",
                "Tio El Bambino ",
                "Dddy Yankee ",
                " Ñengo Flow ",
                " De La Ghetto ",
                " Arcangel ",
                " Farruko ",
                " Baby Rasta & Gringo ",
                " Plan B ",
                " Tempo ",
                " Wisin & Yandel ",
                " Yandel ",
                " J Alvarez ",
                " Lunay ",
                " Rauw Alejandro ",
                " Dalex ",
                " Chencho Corleone ",
                " Rauw Alejandro ",
                " Anuel AA ",
                " Miky Woodz ",
                " Manuel Turizo ",
                " Justin Quiles ",
                " Myke Towers ",
                "Marea",
                "Macaco",
                "Los Delinquentes",
                "El Pescao",
                "El Sueño de Morfeo",
                "Los Planetas",
                "Nena Daconte",
                "Mala Rodríguez",
                "C. Tangana",
                "Leiva"
            );


            $_SESSION['remainingSingers'] = $singers;
        }


        $randomIndex = array_rand($_SESSION['remainingSingers']);
        $randomSinger = $_SESSION['remainingSingers'][$randomIndex];


        unset($_SESSION['remainingSingers'][$randomIndex]);


        $_SESSION['remainingSingers'] = array_values($_SESSION['remainingSingers']);


        $_SESSION['randomSinger'] = $randomSinger;

        return $randomSinger;
    }


    function playHigherLowerGame()
    {

        $clientID = "341643ef6bd645e39f306f9ab420441b";
        $clientSecret = "5fcb10d798dc4f64890eb00f6dced5f6";


        $accessToken = getAccessToken($clientID, $clientSecret);
        if (!$accessToken) {
            echo "Failed to obtain access token. Please try again later.";
            return;
        }


        $alexUbagoInfo = getMonthlyListeners("Alex Ubago", $accessToken);
        if ($alexUbagoInfo === false) {
            echo "Failed to retrieve data for Alex Ubago. Please try again later.";
            return;
        }
        $alexUbagoListeners = $alexUbagoInfo['listeners'];
        $formattedListeners = number_format($alexUbagoListeners, 0, '.', '.');
        $alexUbagoImage = $alexUbagoInfo['image'];


        if (isset($_POST['guess_submit'])) {
            $userGuess = ($_POST['guess_submit'] == 'Más') ? 'higher' : 'lower';
            $currentRandomSinger = $_SESSION['randomSinger'];
            $randomSingerInfo = getMonthlyListeners($currentRandomSinger, $accessToken);
            if ($randomSingerInfo !== false) {
                $randomSingerListeners = $randomSingerInfo['listeners'];
                $formattedRandomListeners = number_format($randomSingerListeners,  0, '.', '.');
                $randomSingerImage = $randomSingerInfo['image'];

                if (($userGuess == 'higher' && $randomSingerListeners > $alexUbagoListeners) ||
                    ($userGuess == 'lower' && $randomSingerListeners < $alexUbagoListeners)
                ) {

                    $_SESSION['score']++;
                } else {
                    echo "<p class='fallo'>Desgraciadamente has fallado. $currentRandomSinger tiene  $formattedRandomListeners oyentes mensuales en Spotify y Álex Ubago tiene $formattedListeners </p>";
                    echo "<p class='score'>Tu puntuación final: {$_SESSION['score']}</p>";
                    echo "<form method='post'><input type='submit' name='restart' value='Jugar de nuevo' class='boton-reinicio'></form>";
                    session_destroy();
                    return;
                }
            } else {
                echo "Failed to retrieve data for $currentRandomSinger. Please try again later.";
                return;
            }
        }


        $randomSinger = getRandomSinger();

        $randomSingerInfo = getMonthlyListeners($randomSinger, $accessToken);
        if ($randomSingerInfo === false) {
            echo "Failed to retrieve data for $randomSinger. Please try again later.";
            return;
        }
        $randomSingerListeners = $randomSingerInfo['listeners'];
        $randomSingerImage = $randomSingerInfo['image'];



        echo "<div class='header'>";
        echo "<p class='alex'><strong>ÁLEX UBAGO COMPARER</strong> </p>";
        echo "<p class='randomSingerText'> $randomSinger tiene ¿más o menos oyentes que Álex Ubago?</p>";
        echo "</div>";


        echo "<div class='image-container'>";
        echo "<img src='$alexUbagoImage' alt='Imagen de Álex Ubago' class='artist-image'>";
        echo "<img src='./src/assets/img/vs.webp' alt='vs' class= 'vs'>";
        echo "<img src='$randomSingerImage' alt='Imagen de $randomSinger' class='artist-image scale-in-center'>";
        echo "</div>";
        echo "<form method='post'>";

        echo "<div class='btn-container'>";
        echo "<input type='submit' name='guess_submit' value='Menos' class='boton-rojo'>";
        echo "<input type='submit' name='guess_submit' value='Más' class='boton-verde'>";
        echo "</div>";
        echo "<div class='aciertos'>";
        echo "<p class='aciertosText'>Aciertos:</p>";
        echo "<p class='score'> {$_SESSION['score']}</p>";
        echo "</div>";
        echo "</form>";
        echo "<footer class='footer'><p>© 2024 Malela Idjabe Makuale. Desarrollado con Angular. Actualizado en abril de 2024.</p></footer>";


        if (isset($_SESSION['score > 0'])) {
            echo "<p>¡Enhorabuena! Has acertado.</p>";
        }
    }


    function getAccessToken($clientID, $clientSecret)
    {

        $tokenUrl = "https://accounts.spotify.com/api/token";


        $headers = array(
            "Authorization: Basic " . base64_encode("$clientID:$clientSecret"),
            "Content-Type: application/x-www-form-urlencoded",
        );


        $data = array(
            "grant_type" => "client_credentials",
        );
        $postData = http_build_query($data);


        $context = stream_context_create([
            'http' => [
                'header' => $headers,
                'method' => 'POST',
                'content' => $postData,
            ]
        ]);
        $response = file_get_contents($tokenUrl, false, $context);


        $data = json_decode($response, true);
        if (isset($data['access_token'])) {
            return $data['access_token'];
        } else {
            return false;
        }
    }

    playHigherLowerGame();
    ?>


</body>

</html>