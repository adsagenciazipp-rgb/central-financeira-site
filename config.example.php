<?php
// Exemplo. O config.php verdadeiro fica apenas no servidor e fora do Git.
return [
    'catalog_url'=>'https://central.naiaracora.com.br/commerce_site_catalog.php',
    'catalog_secret'=>'', // segredo próprio de 32+ caracteres; igual ao commerce_site_catalog_secret da Central.
    'mp_sandbox_checkout_url'=>'https://central.naiaracora.com.br/commerce_mp_sandbox_checkout.php',
    'mp_sandbox_checkout_enabled'=>false, // ativar somente para teste supervisionado; sem cobranças de produção.
];
