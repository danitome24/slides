<?php
/**
 * This software was built by:
 * Daniel Tomé Fernández <danieltomefer@gmail.com>
 * GitHub: danitome24
 */

if (mail('whololo@gmail.com', 'Mailhog example', 'This is an example message')) {
    echo 'Mail enviado correctamente :D';
    return;
}

echo 'Algo malo ha ocurrido :(';
