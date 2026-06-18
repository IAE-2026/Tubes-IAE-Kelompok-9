<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Service C - Prasyarat dan Kurikulum API",
    version: "1.0.0",
    description: "API untuk validasi prasyarat dan kurikulum mahasiswa. Service ini menyediakan akses data nilai mahasiswa, IPS semester, dan detail kurikulum.",
    contact: new OA\Contact(
        name: "Andi Muh. Arif Darma Saputra M",
        email: "102022580023@student.telkomuniversity.ac.id"
    )
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Development Server"
)]
#[OA\SecurityScheme(
    securityScheme: "X-IAE-KEY",
    type: "apiKey",
    in: "header",
    name: "X-IAE-KEY",
    description: "Masukkan API Key MHS (KEY-MHS-117)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Bearer JWT dari IAE SSO (https://iae-sso.virtualfri.id/api/v1/auth/token)"
)]
abstract class Controller
{
    //
}
