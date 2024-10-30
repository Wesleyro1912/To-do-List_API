<?php

// === Verificar se os valores estão vazios ===
function is_null_or_empty($value){
    return !isset($value) || is_null($value) || (is_string($value) && trim($value) === '');  
}

// === Verificar se um número é inteiro ===
if (!function_exists('containsInteger')) {
    
    function containsInteger($value) {
        // Verifica se é um número inteiro diretamente
        if (is_int($value)) {
            return true;
        }
        
        // Verifica se é um número e se não tem casas decimais
        if (is_numeric($value) && (int)$value == $value) {
            return true;
        }
        
        // Caso contrário, não é um número inteiro
        return false;
    }
}