<?php
// contact_functions.php

require_once 'config.php';

function getContactInfo($type = 'all') {
    $query = new Database();
    
    if ($type === 'box') {
        // استرجاع معلومات الاتصال من contact_box
        return $query->select('contact_box', "*");
    } elseif ($type === 'social') {
        // استرجاع وسائل التواصل من contact
        $result = $query->select('contact', "*")[0] ?? [];
        return $result;
    } else {
        // استرجاع جميع البيانات
        return [
            'box' => $query->select('contact_box', "*"),
            'social' => $query->select('contact', "*")[0] ?? []
        ];
    }
}

function getContactBoxByType($type) {
    $query = new Database();
    return $query->select('contact_box', "*", "WHERE type = '{$type}'")[0] ?? null;
}

function getSocialLink($platform) {
    $query = new Database();
    $result = $query->select('contact', "*", "WHERE $platform IS NOT NULL AND $platform != ''")[0] ?? [];
    return $result[$platform] ?? null;
}
?>