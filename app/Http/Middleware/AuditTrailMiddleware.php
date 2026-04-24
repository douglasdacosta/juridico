<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Models\Perfis;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditTrailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $before = null;
        $entity = null;
        $entityId = null;
        $shouldAudit = $this->shouldAudit($request);

        if ($shouldAudit) {
            [$entity, $entityId] = $this->resolveEntityMetadata($request);
            $before = $this->resolveBeforeSnapshot($entity, $entityId);
        }

        $response = $next($request);

        if (! $shouldAudit) {
            return $response;
        }

        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'entity' => $entity,
                'entity_id' => $entityId,
                'action' => $this->resolveAction($request),
                'before' => $this->maskSensitiveData($before),
                'after' => $this->resolveAfterSnapshot($request),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'uri' => $request->path(),
                'status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
            ]);
        } catch (\Throwable $exception) {
        }

        return $response;
    }

    protected function shouldAudit(Request $request): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    protected function resolveAction(Request $request): string
    {
        $path = $request->path();

        if (Str::contains($path, 'incluir-')) {
            return 'create';
        }

        if (Str::contains($path, 'alterar-')) {
            return 'update';
        }

        return match ($request->method()) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($request->method()),
        };
    }

    protected function resolveEntityMetadata(Request $request): array
    {
        $path = $request->path();
        $entity = Str::before($path, '/');
        $entity = Str::replaceFirst('alterar-', '', $entity);
        $entity = Str::replaceFirst('incluir-', '', $entity);

        $entityId = $request->input('id')
            ?? $request->route('id')
            ?? null;

        return [$entity, $entityId ? (string) $entityId : null];
    }

    protected function resolveBeforeSnapshot(?string $entity, ?string $entityId): ?array
    {
        if (empty($entity) || empty($entityId)) {
            return null;
        }

        $modelClass = $this->mapEntityToModelClass($entity);

        if (! $modelClass || ! class_exists($modelClass)) {
            return null;
        }

        $record = $modelClass::query()->find($entityId);

        return $record ? $record->toArray() : null;
    }

    protected function resolveAfterSnapshot(Request $request): array
    {
        return [
            'request' => $this->maskSensitiveData($request->except(['_token'])),
        ];
    }

    protected function mapEntityToModelClass(string $entity): ?string
    {
        $map = [
            'perfis' => Perfis::class,
            'settings' => User::class,
            'users' => User::class,
        ];

        return $map[$entity] ?? null;
    }

    protected function maskSensitiveData($data)
    {
        if (! is_array($data)) {
            return $data;
        }

        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'two_factor_secret',
            'remember_token',
            'token',
        ];

        foreach ($data as $key => $value) {
            if (in_array((string) $key, $sensitiveKeys, true)) {
                $data[$key] = '***';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            }
        }

        return $data;
    }
}
