<?php

return [
    'base_url' => $_ENV['PYTHON_API_URL'] ?? 'http://127.0.0.1:5000',
    'timeout' => (int)($_ENV['PYTHON_API_TIMEOUT'] ?? 30),
];