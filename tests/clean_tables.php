<?php
/**
 * Script per cancellare le tabelle dell'estensione Cash Points System
 * e verificare che l'operazione sia stata completata con successo.
 */

// Configurazione del database
$db_host = 'localhost';
$db_user = 'popologiallorosso1';
$db_pass = 'Popolo-100';
$db_name = 'popologiallorosso1';
$table_prefix = 'phpbb3_';

// Tabelle da eliminare
$tables = [
    $table_prefix . 'cash_points',
    $table_prefix . 'cash_logs',
    $table_prefix . 'cash_settings'
];

// Connessione al database
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception("Connessione fallita: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("<h1>Errore di connessione al database</h1><p>{$e->getMessage()}</p>");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cash Points System - Pulizia Tabelle</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Cash Points System - Pulizia Tabelle</h1>

<?php
// Verifica se le tabelle esistono prima della pulizia
echo '<h2>Stato tabelle prima della pulizia:</h2>';
echo '<table>';
echo '<tr><th>Tabella</th><th>Stato</th></tr>';

$tables_exist_before = [];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    $exists = $result && $result->num_rows > 0;
    $tables_exist_before[$table] = $exists;
    echo '<tr><td>' . $table . '</td><td>' . ($exists ? '<span class="success">Esiste</span>' : '<span class="error">Non esiste</span>') . '</td></tr>';
}
echo '</table>';

// Elimina le tabelle
echo '<h2>Eliminazione tabelle:</h2>';
echo '<table>';
echo '<tr><th>Tabella</th><th>Risultato</th></tr>';

foreach ($tables as $table) {
    if ($tables_exist_before[$table]) {
        try {
            $result = $conn->query("DROP TABLE {$table}");
            if ($result) {
                echo '<tr><td>' . $table . '</td><td><span class="success">Eliminata con successo</span></td></tr>';
            } else {
                echo '<tr><td>' . $table . '</td><td><span class="error">Errore: ' . $conn->error . '</span></td></tr>';
            }
        } catch (Exception $e) {
            echo '<tr><td>' . $table . '</td><td><span class="error">Errore: ' . $e->getMessage() . '</span></td></tr>';
        }
    } else {
        echo '<tr><td>' . $table . '</td><td><span class="info">Non esisteva</span></td></tr>';
    }
}
echo '</table>';

// Verifica se le tabelle esistono dopo la pulizia
echo '<h2>Stato tabelle dopo la pulizia:</h2>';
echo '<table>';
echo '<tr><th>Tabella</th><th>Stato</th></tr>';

$all_deleted = true;
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    $exists = $result && $result->num_rows > 0;
    if ($exists) {
        $all_deleted = false;
    }
    echo '<tr><td>' . $table . '</td><td>' . ($exists ? '<span class="error">Esiste ancora</span>' : '<span class="success">Eliminata</span>') . '</td></tr>';
}
echo '</table>';

// Riepilogo
echo '<h2>Riepilogo:</h2>';
if ($all_deleted) {
    echo '<p class="success">Tutte le tabelle sono state eliminate con successo.</p>';
} else {
    echo '<p class="error">Alcune tabelle non sono state eliminate. Controlla i dettagli sopra.</p>';
}

// Chiudi la connessione al database
$conn->close();
?>

<p>Per ricreare le tabelle, disabilita e riabilita l'estensione Cash Points System dal pannello di amministrazione.</p>
<p><a href="/web/adm/index.php?i=acp_extensions&mode=main">Torna alla gestione estensioni</a></p>

</body>
</html>
