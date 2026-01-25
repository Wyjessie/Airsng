<?php
$host = 'localhost';
$dbname = 'luggage_storage';
$username = 'luggage_app';
$password = 'strongpassword123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<?php
function getCoordinates($postalCode) {
    $url = "https://www.onemap.gov.sg/api/common/elastic/search?searchVal=" . urlencode($postalCode) . "&returnGeom=Y&getAddrDetails=Y&pageNum=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!empty($data['results'])) {
        return [
            'lat' => $data['results'][0]['LATITUDE'],
            'lng' => $data['results'][0]['LONGITUDE'],
            'name'  => $data['results'][0]['SEARCHVAL']
        ];
    }
    return null;
}
