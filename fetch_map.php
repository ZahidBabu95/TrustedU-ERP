<?php
$contents = @file_get_contents('https://raw.githubusercontent.com/shameemreza/bangladesh-map-using-svg/master/bd-map.svg');
if ($contents) {
    file_put_contents('bd-map.svg', $contents);
    echo "Downloaded bd-map.svg\n";
} else {
    echo "Failed to download bd-map.svg\n";
}

$contents2 = @file_get_contents('https://upload.wikimedia.org/wikipedia/commons/4/43/Bangladesh_location_map.svg');
if ($contents2) {
    file_put_contents('bd-map2.svg', $contents2);
    echo "Downloaded bd-map2.svg\n";
} else {
    echo "Failed to download bd-map2.svg\n";
}
