<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache reset.\n";
} else {
    echo "Opcache not enabled.\n";
}
