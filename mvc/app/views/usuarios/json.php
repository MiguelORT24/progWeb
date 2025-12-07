<?php
    header('Content-Type:application/json');
    header('Content-Disposition:attachment;filename=usuarios.json');
    
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_IGNORE );


?>