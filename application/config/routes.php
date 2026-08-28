<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'dashboard';
$route['login'] = 'login';
$route['logout'] = 'login/logout';
$route['forgot_password'] = 'login/forgot_password';
$route['ajukan-booking'] = 'dashboard/ajukan';
$route['kalender'] = 'dashboard/kalender';
// Mahasiswa Routes
$route['mahasiswa/detail'] = 'Mahasiswa/detail_pendaftaran';
$route['mahasiswa/detail_pendaftaran'] = 'Mahasiswa/detail_pendaftaran';
$route['mahasiswa/edit'] = 'Mahasiswa/edit_pendaftaran';
$route['mahasiswa/edit_pendaftaran'] = 'Mahasiswa/edit_pendaftaran';

// Dosen Wali Routes
$route['dosenwali'] = 'DosenWali';
$route['dosenwali/detail_mahasiswa/(:any)'] = 'DosenWali/detail_mahasiswa/$1';
$route['dosenwali/(:any)'] = 'DosenWali/$1';
$route['dosenwali/(:any)/(:any)'] = 'DosenWali/$1/$2';

// Koordinator TA Routes
$route['koordinatorta'] = 'KoordinatorTA';
$route['koordinatorta/detail_mahasiswa/(:any)'] = 'KoordinatorTA/detail_mahasiswa/$1';
$route['koordinatorta/(:any)'] = 'KoordinatorTA/$1';
$route['koordinatorta/(:any)/(:any)'] = 'KoordinatorTA/$1/$2';
$route['koordinator'] = 'KoordinatorTA';
$route['koordinator/detail_mahasiswa/(:any)'] = 'KoordinatorTA/detail_mahasiswa/$1';
$route['koordinator/(:any)'] = 'KoordinatorTA/$1';
$route['koordinator/(:any)/(:any)'] = 'KoordinatorTA/$1/$2';

// Admin Import Email & Token Routes
$route['import-email'] = 'ImportEmail';
$route['import-email/(:any)'] = 'ImportEmail/$1';
$route['admin/import-email'] = 'ImportEmail';
$route['admin/import-email/(:any)'] = 'ImportEmail/$1';
$route['admin/import'] = 'ImportEmail';
$route['admin/import/(:any)'] = 'ImportEmail/$1';

// Admin Kelola Ruangan Routes
$route['kelolaruangan'] = 'Kelolaruangan';
$route['admin/ruangan'] = 'Kelolaruangan';
$route['admin/kelolaruangan'] = 'Kelolaruangan';

// Onboarding & Force Change Password Routes
$route['onboarding'] = 'Onboarding';
$route['onboarding/(:any)'] = 'Onboarding/$1';

// Admin Layanan (LAA) Routes
$route['adminlayanan'] = 'AdminLayanan/index';
$route['adminlayanan/autocomplete'] = 'AdminLayanan/autocomplete';
$route['adminlayanan/detail_berkas/(:any)'] = 'AdminLayanan/detail_berkas/$1';
$route['adminlayanan/submit_verifikasi/(:any)'] = 'AdminLayanan/submit_verifikasi/$1';

// Ketua KK Routes
$route['ketuakk'] = 'KetuaKK/index';
$route['ketuakk/autocomplete'] = 'KetuaKK/autocomplete';
$route['ketuakk/detail/(:any)'] = 'KetuaKK/detail/$1';
$route['ketuakk/submit_approval/(:any)'] = 'KetuaKK/submit_approval/$1';
$route['ketuakk/submit_bulk_approval'] = 'KetuaKK/submit_bulk_approval';

// Central Admin Panel Routes
$route['admin'] = 'Admin/index';

// News / Berita Routes
$route['news/detail/(:num)'] = 'News/detail/$1';
$route['news/newsroom'] = 'News/index';
$route['news/save'] = 'News/save';
$route['news/delete/(:num)'] = 'News/delete/$1';
$route['news/toggle/(:num)'] = 'News/toggle/$1';
$route['news/get_all_json'] = 'News/get_all_json';

// About / Baca Selengkapnya Route
$route['dashboard/about'] = 'Dashboard/about';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;



