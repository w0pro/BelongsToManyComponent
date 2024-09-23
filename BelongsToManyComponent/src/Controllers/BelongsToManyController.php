<?php

namespace Wopro\BelongsToManyComponent\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wopro\BelongsToManyComponent\Requests\BelongsToManyFullRelationsRequest;
use Wopro\BelongsToManyComponent\Requests\BelongsToManyRequest;
use Wopro\BelongsToManyComponent\Requests\BelongsToManySortRequest;
use Wopro\BelongsToManyComponent\Services\BelongsToManyService;


class BelongsToManyController extends Controller
{

    public function __construct(private readonly BelongsToManyService $belongsToManyService)
    {}


    public function getRelations(NovaRequest $request): JsonResponse
    {
        $queryModel = $request->input('exclude');
        $models = $this->belongsToManyService->getModels($queryModel);
        return response()->json($models);
    }


    public function search(NovaRequest $request): JsonResponse
    {
        $queryModel = $request->input('subjectModel');
        $querySearch = $request->input('q')??'';
        $objectId = $request->input('objectId');
        $objectName = $request->input('objectName');
        $models = $this->belongsToManyService->searchModels($queryModel, $querySearch, $objectId, $objectName);

        return response()->json($models);
    }

    public function store(BelongsToManyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $relation = $this->belongsToManyService->store($validated);

        return response()->json($relation);

    }

    public function show(BelongsToManyRequest $request, $id) :JsonResponse
    {
        $validated = $request->validated();
        $relations = $this->belongsToManyService->show($validated, $id);

        return response()->json($relations);
    }

    public function destroy(BelongsToManyRequest $request, $id)
    {
        $validated = $request->validated();
        $deleteModelData  = $this->belongsToManyService->destroy($validated, $id);
        return response()->json($deleteModelData);
    }

    public function sort(BelongsToManySortRequest $request)
    {
        $validated = $request->validated();
        $this->belongsToManyService->sort($validated);
        return response()->json();
    }

    public function index(BelongsToManyFullRelationsRequest $request, $id):JsonResponse
    {
        $validated = $request->validated();
        $relations = $this->belongsToManyService->index($validated, $id);

        return response()->json($relations);
    }

    public function searchAll(NovaRequest $request): JsonResponse
    {
        $queryModel = $request->input('objectName');
        $queryModelId = $request->input('objectId');
        $querySearch = $request->input('q');
        $models = $this->belongsToManyService->searchAllModels($queryModel, $querySearch, $queryModelId);

        return response()->json($models);
    }

    public function getRows(NovaRequest $request, $id): JsonResponse
    {
        $subjectModel = $request->input('subjectModel');
        $queryModel = $request->input('objectName');
        $modelRows = $this->belongsToManyService->getRows($subjectModel, $queryModel, $id);
        return response()->json($modelRows);
    }

    public function update(NovaRequest $request, $id, $objectName, $subjectModel ): JsonResponse
    {
       $data = $request->except(['_token']);
        $modelRows = $this->belongsToManyService->update($subjectModel, $objectName, $id, $data);
        return response()->json($modelRows);
    }




}
