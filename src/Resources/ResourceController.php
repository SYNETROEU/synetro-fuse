<?php

declare(strict_types=1);

namespace Synetro\Fuse\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class FuseResourceController
{
    public function __construct(
        protected ResourceService $service,
        protected ResourceDefinition $resource,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny');

        $perPage = (int) ($request->input('per_page', $this->resource->paginate()));

        $query = (new ResourceQuery($this->resource))->apply($request->all());

        $paginator = $this->service->paginate($this->resource->model(), $query, $perPage);

        return (new ResourceResponse)->index($paginator);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create');

        $this->validate($request);

        $model = $this->service->create($this->resource->model(), $request->all());

        return (new ResourceResponse)->created($model);
    }

    public function show(Model $model): JsonResponse
    {
        $this->authorize('view', $model);

        return (new ResourceResponse)->show($model);
    }

    public function update(Request $request, Model $model): JsonResponse
    {
        $this->authorize('update', $model);

        $this->validate($request);

        $model = $this->service->update($this->resource->model(), $model->getKey(), $request->all());

        return (new ResourceResponse)->updated($model);
    }

    public function destroy(Model $model): JsonResponse
    {
        $this->authorize('delete', $model);

        $this->service->delete($this->resource->model(), $model->getKey());

        return (new ResourceResponse)->deleted();
    }

    protected function authorize(string $ability, mixed ...$arguments): void
    {
        (new ResourcePolicy($this->resource->policy(), $this->resource->authorize()))
            ->check($ability, auth()->user(), ...$arguments);
    }

    protected function validate(Request $request): void
    {
        Validator::make($request->all(), [])->validate();
    }
}
