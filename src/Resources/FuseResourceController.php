<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FuseResourceController
{
    public function __construct(
        protected ResourceService $service,
        protected ResourceManager $resources,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $this->authorize('viewAny', $resource);

        $perPage = (int) ($request->input('per_page', $resource->paginate()));

        $query = (new ResourceQuery($resource))->apply($request->all());

        $paginator = $this->service->paginate($resource->model(), $query, $perPage);

        return (new ResourceResponse)->index($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $this->authorize('create', $resource);

        $model = $this->service->create($resource->model(), $request->all());

        return (new ResourceResponse)->created($model);
    }

    public function show(Request $request, mixed $id): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $model = $this->service->find($resource->model(), $id);

        $this->authorize('view', $model);

        return (new ResourceResponse)->show($model);
    }

    public function update(Request $request, mixed $id): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $this->authorize('update', $resource);

        $model = $this->service->update($resource->model(), $id, $request->all());

        return (new ResourceResponse)->updated($model);
    }

    public function destroy(Request $request, mixed $id): JsonResponse
    {
        $resource = $this->resolveResource($request);

        $this->authorize('delete', $resource);

        $this->service->delete($resource->model(), $id);

        return (new ResourceResponse)->deleted();
    }

    protected function resolveResource(Request $request): ResourceDefinition
    {
        $uri = $request->route()->getPrefix();

        $resource = $this->resources->getByUri($uri);

        if (! $resource) {
            throw new \RuntimeException("Resource not found for URI: {$uri}");
        }

        return $resource;
    }

    protected function authorize(string $ability, mixed $arguments = []): void
    {
        $resource = $this->resolveResource(request());

        (new ResourcePolicy($resource->policy(), $resource->authorize()))
            ->check($ability, $arguments);
    }
}
