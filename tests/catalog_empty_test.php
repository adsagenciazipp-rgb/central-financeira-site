<?php
declare(strict_types=1);
require dirname(__DIR__).'/private/catalog_bridge.php';
$source=file_get_contents(dirname(__DIR__).'/private/catalog_bridge.php');
if(!is_string($source)||!str_contains($source,"'EMPTY_CATALOG'")||!str_contains($source,"'INVALID_PLAN_DATA'"))throw new RuntimeException('Faltam status separados de catálogo vazio e dados inválidos.');
$checkout=file_get_contents(dirname(__DIR__).'/checkout.php');
if(!is_string($checkout)||!str_contains($checkout,'nenhum plano válido foi retornado'))throw new RuntimeException('Falta mensagem ao operador.');
echo "OK: mensagem e códigos de diagnóstico para lista de planos vazia.\n";
