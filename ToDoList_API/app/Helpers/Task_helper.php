<?php

// === Verificar se os valores estão vazios ===
function is_null_or_empty($value){
    return !isset($value) || is_null($value) || (is_string($value) && trim($value) === '');  
}

// === Função para validação do ID ===
function isValidId($id){
    return is_numeric($id) && (int)$id > 0 && strlen((string)$id) <= 5;
}