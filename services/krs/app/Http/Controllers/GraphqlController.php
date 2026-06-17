<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GraphqlController extends Controller
{
    private const ALLOWED_FIELDS = [
        'id',
        'nim',
        'kode_mata_kuliah',
        'nama_mata_kuliah',
        'sks',
        'tahun_ajaran',
        'semester',
        'status_persetujuan',
        'catatan',
        'created_at',
        'updated_at',
    ];

    public function handle(Request $request): JsonResponse
    {
        $query = (string) $request->input('query', '');

        if (trim($query) === '') {
            return $this->graphQlError('Query GraphQL wajib diisi.', 422);
        }

        if (Str::contains($query, '__schema')) {
            return response()->json(['data' => $this->introspection()]);
        }

        if (preg_match('/\bkrsList\b\s*\{(?P<fields>[^}]*)\}/s', $query, $matches)) {
            $fields = $this->fieldsFrom($matches['fields']);
            $items = Krs::query()->latest()->get()->map(
                fn (Krs $krs): array => $this->shape($krs, $fields)
            );

            return response()->json(['data' => ['krsList' => $items]]);
        }

        if (preg_match('/\bkrs\s*\(\s*id\s*:\s*(?P<id>[^)]+)\)\s*\{(?P<fields>[^}]*)\}/s', $query, $matches)) {
            $id = $this->resolveId($matches['id'], (array) $request->input('variables', []));

            if (! $id) {
                return $this->graphQlError('ID KRS wajib diisi.', 422);
            }

            $fields = $this->fieldsFrom($matches['fields']);
            $krs = Krs::query()->find($id);

            return response()->json([
                'data' => [
                    'krs' => $krs ? $this->shape($krs, $fields) : null,
                ],
            ]);
        }

        return $this->graphQlError('Query GraphQL tidak didukung. Gunakan krsList atau krs(id: ID!).', 422);
    }

    private function fieldsFrom(string $selection): array
    {
        $fields = preg_split('/\s+/', trim($selection)) ?: [];
        $fields = array_values(array_intersect($fields, self::ALLOWED_FIELDS));

        return $fields ?: ['id', 'nim', 'kode_mata_kuliah', 'nama_mata_kuliah', 'sks', 'tahun_ajaran', 'semester'];
    }

    private function resolveId(string $rawId, array $variables): ?string
    {
        $rawId = trim($rawId);

        if ($rawId === '$id') {
            return isset($variables['id']) ? (string) $variables['id'] : null;
        }

        return trim($rawId, "\"' ");
    }

    private function shape(Krs $krs, array $fields): array
    {
        $data = [];

        foreach ($fields as $field) {
            $value = $krs->{$field};

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(DATE_ATOM);
            }

            $data[$field] = $value;
        }

        return $data;
    }

    private function introspection(): array
    {
        return [
            '__schema' => [
                'queryType' => ['name' => 'Query'],
                'types' => [
                    ['name' => 'Query', 'kind' => 'OBJECT'],
                    ['name' => 'Krs', 'kind' => 'OBJECT'],
                ],
            ],
        ];
    }

    private function graphQlError(string $message, int $status): JsonResponse
    {
        return response()->json([
            'errors' => [
                ['message' => $message],
            ],
        ], $status);
    }
}
