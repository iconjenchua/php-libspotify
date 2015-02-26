<?php

require 'Spotify.php';

$key = 'aa780885c3ae49b081f642b3806bb2df';
$secret = '3bd0953f37c5424b88a50ce5438acf31';

$api = new Spotify($key, $secret);

if(!$api->authorize())
{
    return 'Oops! Something went wrong: ' . $api->error_description . '.';
}

$artists = $api->getArtist(array('2XHTklRsNMOOQT56Zm3WS4'));

print_r($artists);