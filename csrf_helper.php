<?php
require_once 'includes/session_config.php';

function generate_csrf_token()
{
    return ''; // CSRF disabled
}

/**
 * Verifies the CSRF token.
 * @param string $token The token to verify.
 * @return bool True if valid, false otherwise.
 */
function verify_csrf_token($token)
{
    return true; // CSRF disabled
}
