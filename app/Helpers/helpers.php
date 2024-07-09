<?php

function jsonResponse($data = [], $status = 200, $message = 'ok', $errors = []){
    return response()->json(compact('data', 'status', 'message','errors'), $status);
}
