<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test / when not logged in
$request = \Illuminate\Http\Request::create('/', 'GET');
$session = $app->make('session')->driver();
$request->setLaravelSession($session);
$session->start();
$response = $kernel->handle($request);
echo "/ (not logged in): {$response->getStatusCode()} → " . $response->headers->get('Location') . PHP_EOL;

// Test / when logged in
$admin = \App\Models\Admin::first();
$request = \Illuminate\Http\Request::create('/', 'GET');
$session = $app->make('session')->driver();
$request->setLaravelSession($session);
$session->start();
auth('admin')->login($admin);
$response = $kernel->handle($request);
echo "/ (logged in): {$response->getStatusCode()} → " . $response->headers->get('Location') . PHP_EOL;

// Test /admin/login when logged in
$request = \Illuminate\Http\Request::create('/admin/login', 'GET');
$session = $app->make('session')->driver();
$request->setLaravelSession($session);
$session->start();
auth('admin')->login($admin);
$response = $kernel->handle($request);
echo "/admin/login (logged in): {$response->getStatusCode()} → " . $response->headers->get('Location') . PHP_EOL;
