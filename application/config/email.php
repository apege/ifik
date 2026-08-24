<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| EMAIL CONFIGURATION (SMTP Protocol)
| -------------------------------------------------------------------
| Konfigurasi pengiriman email untuk notifikasi token akun portal IFIK
| Mendukung server Gmail, Office365, Mailtrap, atau Mail Server Telkom University.
*/

$config['protocol']     = 'smtp';
$config['smtp_host']    = getenv('SMTP_HOST') ? getenv('SMTP_HOST') : 'smtp.gmail.com';
$config['smtp_port']    = getenv('SMTP_PORT') ? (int)getenv('SMTP_PORT') : 587;
$config['smtp_user']    = getenv('SMTP_USER') ? getenv('SMTP_USER') : 'fik.telkomuniversity@gmail.com';
$config['smtp_pass']    = getenv('SMTP_PASS') ? getenv('SMTP_PASS') : '';
$config['smtp_crypto']  = getenv('SMTP_CRYPTO') ? getenv('SMTP_CRYPTO') : 'tls';
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['wordwrap']     = TRUE;
$config['smtp_timeout'] = 10;
