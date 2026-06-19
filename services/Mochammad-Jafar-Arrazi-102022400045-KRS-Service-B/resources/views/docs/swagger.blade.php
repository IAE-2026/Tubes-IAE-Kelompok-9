<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Swagger UI - KRS-Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; background: #f7f7f7; }
        .topbar { display: none; }
        .fallback { padding: 16px 24px; font-family: Arial, sans-serif; background: #111827; color: #fff; }
        .fallback a { color: #93c5fd; }
    </style>
</head>
<body>
    <div class="fallback">
        Swagger UI KRS-Service. OpenAPI JSON tersedia di
        <a href="/docs/openapi.json">/docs/openapi.json</a>.
    </div>
    <div id="swagger-ui"></div>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.ui = SwaggerUIBundle({
            url: '/docs/openapi.json',
            dom_id: '#swagger-ui',
            deepLinking: true,
            persistAuthorization: true
        });
    </script>
</body>
</html>
