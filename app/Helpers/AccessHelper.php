<?php

if (! function_exists('canAccess')) {
    function canAccess($permission)
    {
        $user = auth()->user();
        return $user && $user->canAccess($permission);
    }
}
