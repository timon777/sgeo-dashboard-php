<?php
/**
 * Supabase Database Configuration
 */

return [
    'supabase_url' => getenv('SUPABASE_URL') ?: '',
    'supabase_key' => getenv('SUPABASE_KEY') ?: '',
    'supabase_service_key' => getenv('SUPABASE_SERVICE_KEY') ?: '',
];
