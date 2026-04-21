<?php

if (!function_exists('env_value')) {
    function env_value(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $value = getenv($key);

        return $value === false ? $default : $value;
    }
}

if (!function_exists('send_mail')) {
    function send_mail(string $to, string $subject, string $body): bool
    {
        $driver = env_value('MAIL_DRIVER', 'mail');
        $fromAddress = env_value('MAIL_FROM_ADDRESS', 'noreply@localhost');
        $fromName = env_value('MAIL_FROM_NAME', 'EOIM Platform');

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'From: ' . $fromName . ' <' . $fromAddress . '>';
        $headers[] = 'Reply-To: ' . $fromAddress;

        if ($driver === 'smtp') {
            return send_mail_smtp($to, $subject, $body, $fromAddress, $headers);
        }

        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
}

if (!function_exists('send_mail_smtp')) {
    function send_mail_smtp(string $to, string $subject, string $body, string $fromAddress, array $headers): bool
    {
        $host = env_value('MAIL_HOST', '127.0.0.1');
        $port = (int)env_value('MAIL_PORT', 25);
        $username = env_value('MAIL_USERNAME', '');
        $password = env_value('MAIL_PASSWORD', '');
        $encryption = env_value('MAIL_ENCRYPTION', '');
        $timeout = 30;

        $remoteSocket = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
        $connection = stream_socket_client($remoteSocket, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);

        if (!$connection) {
            app_log("SMTP connect failed: {$errno} {$errstr}");
            return false;
        }

        stream_set_timeout($connection, $timeout);
        if (!smtp_expect($connection, 220)) {
            return false;
        }

        $hostname = gethostname() ?: 'localhost';
        smtp_send($connection, "EHLO {$hostname}\r\n");
        if ($encryption === 'tls') {
            smtp_send($connection, "STARTTLS\r\n");
            if (!stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                app_log('Failed to enable TLS for SMTP connection');
                return false;
            }
            smtp_send($connection, "EHLO {$hostname}\r\n");
        }

        if ($username !== '' && $password !== '') {
            smtp_send($connection, "AUTH LOGIN\r\n");
            smtp_expect($connection, 334);
            smtp_send($connection, base64_encode($username) . "\r\n");
            smtp_expect($connection, 334);
            smtp_send($connection, base64_encode($password) . "\r\n");
            smtp_expect($connection, 235);
        }

        smtp_send($connection, "MAIL FROM:<{$fromAddress}>\r\n");
        smtp_expect($connection, 250);
        smtp_send($connection, "RCPT TO:<{$to}>\r\n");
        smtp_expect($connection, 250);
        smtp_send($connection, "DATA\r\n");
        smtp_expect($connection, 354);

        $headers[] = 'Subject: ' . $subject;
        $headers[] = 'To: ' . $to;
        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
        smtp_send($connection, $message);
        smtp_expect($connection, 250);

        smtp_send($connection, "QUIT\r\n");
        fclose($connection);

        return true;
    }
}

if (!function_exists('smtp_send')) {
    function smtp_send($connection, string $command): void
    {
        fwrite($connection, $command);
    }
}

if (!function_exists('smtp_expect')) {
    function smtp_expect($connection, int $expectedCode): bool
    {
        $response = '';

        while (($line = fgets($connection, 512)) !== false) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        if (substr($response, 0, 3) !== (string)$expectedCode) {
            app_log('SMTP server response: ' . trim($response));
            return false;
        }

        return true;
    }
}
