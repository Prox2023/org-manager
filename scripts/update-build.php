<?php

$buildFile = __DIR__ . '/../build.json';

$buildInfo = file_exists($buildFile) 
    ? json_decode(file_get_contents($buildFile), true) 
    : [];

$buildInfo['build'] = time();
$buildInfo['version'] = $buildInfo['version'] ?? '1.0.0';

file_put_contents($buildFile, json_encode($buildInfo, JSON_PRETTY_PRINT));

echo "Build number updated to: {$buildInfo['build']}\n"; 