<?php
// check.php
echo "<pre>";
echo "PHP: ".PHP_VERSION."\n";

echo "Extension mongodb: ".(extension_loaded('mongodb') ? "OK" : "ABSENTE")."\n";
echo "Autoload Composer: ".(file_exists(__DIR__.'/vendor/autoload.php') ? "present" : "absent")."\n";

if (!file_exists(__DIR__.'/vendor/autoload.php')) {
  echo "=> Lance: composer require mongodb/mongodb\n";
  exit;
}

require __DIR__.'/vendor/autoload.php';

echo "Classe MongoDB\\Client: ".(class_exists(MongoDB\Client::class) ? "OK" : "NON TROUVÉE")."\n";

try {
  $client = new MongoDB\Client('mongodb://127.0.0.1:27017');
  $count  = $client->selectDatabase('agridata')->selectCollection('maps')->countDocuments();
  echo "Connexion Mongo OK, documents dans agridata.maps: $count\n";
} catch (Throwable $e) {
  echo "ERREUR Mongo: ".$e->getMessage()."\n";
}