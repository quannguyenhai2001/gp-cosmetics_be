<?php
function checkServerError($isCondition)
{
    if (!$isCondition) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Server error!"
        ]);
        return true;
    }
    return false;
}
