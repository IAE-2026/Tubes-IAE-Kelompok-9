<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GraphiQL - KRS-Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/graphiql@3/graphiql.min.css">
    <style>
        html, body, #graphiql { height: 100%; margin: 0; }
        .fallback { padding: 12px 16px; font-family: Arial, sans-serif; background: #111827; color: #fff; }
    </style>
</head>
<body>
    <div class="fallback">GraphiQL KRS-Service. Header default: X-IAE-KEY 102022400045.</div>
    <div id="graphiql"></div>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://cdn.jsdelivr.net/npm/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/graphiql@3/graphiql.min.js"></script>
    <script>
        const fetcher = GraphiQL.createFetcher({
            url: '/graphql',
            headers: {
                'Content-Type': 'application/json',
                'X-IAE-KEY': '102022400045'
            }
        });

        ReactDOM.createRoot(document.getElementById('graphiql')).render(
            React.createElement(GraphiQL, {
                fetcher,
                defaultQuery: `query {
  krsList {
    id
    nim
    kode_mata_kuliah
    nama_mata_kuliah
    status_persetujuan
  }
}`
            })
        );
    </script>
</body>
</html>
