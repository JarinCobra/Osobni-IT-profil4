<?php
// Cesta k souboru s daty
$file = 'profile.json';
$message = "";
$messageType = "";

// Načtení dat ze souboru (ošetřeno pro případ, že soubor neexistuje)
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
} else {
    $data = ['interests' => []];
}
$interests = $data['interests'] ?? [];

// 1. Ověření, že byl formulář odeslán
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_interest"])) {
    
    // 2. Očištění vstupu
    $newInterest = trim($_POST["new_interest"]);

    // 3. Kontrola, že není prázdný
    if (empty($newInterest)) {
        $message = "Pole nesmí být prázdné.";
        $messageType = "error";
    } else {
        // 4. Zabránění duplicitě (převod na malá písmena pro porovnání)
        $lowerInterests = array_map('mb_strtolower', $interests);
        
        if (in_array(mb_strtolower($newInterest), $lowerInterests)) {
            $message = "Tento zájem už existuje.";
            $messageType = "error";
        } else {
            // 5. Přidání nového zájmu do pole
            $interests[] = $newInterest;
            $data['interests'] = $interests;

            // 6. Uložit změny do profile.json
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $message = "Zájem byl úspěšně přidán.";
            $messageType = "success";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>IT Profil 4.0</title>
</head>
<body>

    <?php if (!empty($message)): ?>
        <p class="<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="new_interest" required>
        <button type="submit">Přidat zájem</button>
    </form>

    <hr>
    <h3>Moje zájmy:</h3>
    <ul>
        <?php foreach ($interests as $item): ?>
            <li><?php echo htmlspecialchars($item); ?></li>
        <?php endforeach; ?>
    </ul>

</body>
</html>