<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * Verifies the SMTP config in .env (email.*) actually works from wherever
 * this is run — run it again on the production server before relying on
 * email notifications there, since outbound SMTP (port 587) is sometimes
 * blocked by hosting firewalls even when it works fine locally.
 *
 * Usage: php spark test:email [recipient@example.com]
 */
class TestEmail extends BaseCommand
{
    protected $group       = 'test';
    protected $name        = 'test:email';
    protected $description = 'Send a test email to verify the SMTP config in .env works from this server.';
    protected $usage       = 'test:email [recipient]';

    public function run(array $params)
    {
        $to = $params[0] ?? 'admin.lazismusragen@gmail.com';

        $email = Services::email();
        $email->setTo($to);
        $email->setSubject('Test Email - Lazismu Sragen App');
        $email->setMessage('<p>Ini adalah email uji coba dari aplikasi Lazismu Sragen. Jika Anda menerima email ini, konfigurasi SMTP sudah benar.</p><p>Waktu kirim: ' . date('Y-m-d H:i:s') . '</p>');

        $ok = $email->send();

        if ($ok) {
            CLI::write('SUCCESS: email sent to ' . $to, 'green');
        } else {
            CLI::write('FAILED to send email.', 'red');
            CLI::write(print_r($email->printDebugger(), true));
        }
    }
}
