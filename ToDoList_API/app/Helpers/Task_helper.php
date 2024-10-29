<?php

// === Verificar se os valores estão vazios ===
function is_null_or_empty($value){
    return !isset($value) || is_null($value) || (is_string($value) && trim($value) === '');  
}