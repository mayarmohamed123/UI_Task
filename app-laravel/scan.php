<?php
$lines = file(__DIR__ . '/resources/views/mosabka/judgings/tafseer/index.blade.php');
$stack = [];
foreach ($lines as $i => $line) {
    if (preg_match('/@(if|foreach|auth|guest)\b/', $line, $matches)) {
        $stack[] = ['type' => $matches[1], 'line' => $i + 1, 'text' => trim($line)];
    } elseif (preg_match('/@(endif|endforeach|endauth|endguest)\b/', $line, $matches)) {
        array_pop($stack);
    }
}
print_r($stack);
