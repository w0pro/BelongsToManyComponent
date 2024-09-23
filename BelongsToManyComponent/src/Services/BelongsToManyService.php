<?php

namespace Wopro\BelongsToManyComponent\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BelongsToManyService
{
    /**
     * @param $excludeModel
     * @return string
     * Получаем название модели по названию ресурса из Nova
     */
    public function getModelName($excludeModel): string
    {
        $path = app_path('Models');
        $files = File::allFiles($path);
        $name= '';

        foreach ($files as $file) {
            $modelName = pathinfo($file, PATHINFO_FILENAME);

            $instanceModel = app('App\Models\\'.$modelName);
            if ($instanceModel->getTable() === $excludeModel) {
                return $modelName;
            }
        }

        return $name;
    }

    /**
     * @param string $queryModel
     * @param string|null $modelQueryName
     * @return array
     * Поверяем наличие промежуточной модели и возвращаем модель с которой есть промежуточная связь
     */

    public function getModels(string $queryModel, string $modelQueryName = null): array
    {
        $excludeModelName = $queryModel?$this->getModelName($queryModel):null;

        $models = [];
        $path = app_path('Models');
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $modelName = $modelQueryName === null?pathinfo($file, PATHINFO_FILENAME):$modelQueryName;
            if ((class_exists('App\Models\\'.$excludeModelName.$modelName)) ||
                (class_exists('App\Models\\'.$modelName.$excludeModelName))
            ) {
                $models[] = $modelName;
            }
        }

        return $models;
    }

    /**
     * @param string $queryModel
     * @param string $querySearch
     * @return mixed
     *
     * Поиск всех моделей интсанса сущности
     */

    public function searchModels( string $queryModel, string $querySearch, int $objectId, string $objectName)
    {
        $objectModelName = $this->getModelName($objectName);
        $relationBuilder = $this->getRelationModel($objectModelName, $queryModel);
        $excludeIds = $relationBuilder::where(strtolower($objectModelName).'_id', $objectId)
            ->get()->pluck(strtolower($queryModel).'_id')
            ->toArray();

        $instance = app('App\Models\\'.$queryModel);

        return $instance::search($querySearch)
            ->whereNotIn('id', $excludeIds)
            ->get()
            ->makeHidden(['created_at', 'updated_at']);
    }

    /**
     * @param string $objectName
     * @param string $subjectModel
     * @return \Illuminate\Foundation\Application|mixed|null
     *
     * Получение промежуточной модели
     */

    public function getRelationModel(string $objectModel,string $subjectModel)
    {
        $relationModel = null;

        if (class_exists('App\Models\\' . $objectModel . $subjectModel)) {
            $relationModel = app('App\Models\\' . $objectModel . $subjectModel);
        }elseif (class_exists('App\Models\\' . $subjectModel . $objectModel)) {
            $relationModel =  app('App\Models\\' . $subjectModel . $objectModel);
        }

        return $relationModel;
    }

    /**
     * @param array $validated
     * @return array
     *
     * Создание отношения
     */


    public function store(array $validated)
    {
        $objectModelName = $this->getModelName($validated['objectName']);

        $relationModel = $this->getRelationModel($objectModelName, $validated['subjectModel']);

        $exist = $relationModel::where([
            strtolower($objectModelName).'_id' => $validated['objectId'],
            strtolower($validated['subjectModel']).'_id' => $validated['subjectId']
        ])->exists();

        $subjectModelBuilder = app('App\Models\\'.$validated['subjectModel']);

        $subjectModel = [];

        if (!$exist) {
            $relationModel::create([
                strtolower($objectModelName).'_id' => $validated['objectId'],
                strtolower($validated['subjectModel']).'_id' => $validated['subjectId']
            ]);

            $subjectModel = $subjectModelBuilder
                ->find((int)$validated['subjectId'])
                ->makeHidden(['created_at', 'updated_at']);
        }
        return $subjectModel;
    }

    /**
     * @param array $validated
     * @param int $id
     * @return mixed
     *
     * Получение существующих отношений между двумя сущностями
     */

    public function show(array $validated, int $id)
    {
        $objectModelName = $this->getModelName($validated['objectName']);
        return $this->getRelationModelsList($objectModelName, $validated['subjectModel'], $id);
    }

    /**
     * @param array $validated
     * @param int $id
     * @return array
     *
     * Удаление отношений
     */

    public function destroy(array $validated, int $id)
    {
        $objectModelName = $this->getModelName($validated['objectName']);
        $relationModel = $this->getRelationModel($objectModelName, $validated['subjectModel']);

        $existModel = $relationModel::where([
            strtolower($objectModelName).'_id' => $id,
            strtolower($validated['subjectModel']).'_id' => $validated['subjectId']
        ])->first();

        if ($existModel) {
            $existModel->delete();
            return [
                'id' => (int) $validated['subjectId'],
                'model' => $validated['subjectModel']
            ];
        }else {
            return [
                'id' => 0,
                'model' => $validated['subjectModel']
            ];
        }

    }

    /**
     * @param array $validated
     * @return void
     * Пересортировка
     */

    public function sort(array $validated):void
    {
        $objectModelName = $this->getModelName($validated['objectName']);
        $validated['sortData'] = array_map(function ($el){
            return $el;
        }, $validated['sortData']);

        $relationModel = $this->getRelationModel($objectModelName, $validated['subjectModel']);
        $relationModel::upsert($validated['sortData'], ['id'], ['order_id']);
    }

    /**
     * @param string $objectModelName
     * @param string $subjectModel
     * @param int $id
     * @return mixed
     * Получение моделей существующей связи
     */

    public function getRelationModelsList(string $objectModelName, string $subjectModel, int $id)
    {
        $relationModelBuilder = $this->getRelationModel($objectModelName, $subjectModel);

        $relationModelSubjectIds = $relationModelBuilder
            ->where([strtolower($objectModelName).'_id' => $id])
            ->get()
            ->pluck(strtolower($subjectModel).'_id');
        $subjectModelBuilder = app('App\Models\\'.$subjectModel);

        $relationModelTableName = $relationModelBuilder->getTable();
        $subjectModelTableName = $subjectModelBuilder->getTable();

        return $subjectModelBuilder
            ->whereIn($subjectModelTableName.'.id', $relationModelSubjectIds)
            ->join($relationModelTableName,$subjectModelTableName.'.id', '=', $relationModelTableName.'.'.strtolower($subjectModel).'_id')
            ->select($subjectModelTableName.'.*', $relationModelTableName.'.order_id', $relationModelTableName.'.id as relation_id')
            ->orderBy($relationModelTableName.'.order_id', 'asc')
            ->paginate(10);
    }

    /**
     * @param array $validated
     * @param int $id
     * @return array
     *
     * Получение всех существующих отношений с разными сущностями
     */

    public function index(array $validated, int $id): array
    {
        $objectModelName = $this->getModelName($validated['objectName']);
        $models = $this->getModels(($validated['objectName']));
        $relationModels = [];
        if ($models) {
            foreach ($models as $model) {
                $relationModels = array_merge($this->getRelationModelsList($objectModelName,$model, $id)->toArray(), $relationModels);
                $relationModels = array_map(function ($el) use ($model){
                    $el['subjectModel'] = $model;
                    return $el;
                },$relationModels);
            }
        }

        return $relationModels;

    }

    /**
     * @param string $queryModel
     * @param $querySearch
     * @param int $queryModelId
     * @return array
     *
     * Поиск по всем существующим связям
     */

    public function searchAllModels( string $queryModel, $querySearch, int $queryModelId): array
    {
        $objectModelName = $this->getModelName($queryModel);

        $models = $this->getModels($queryModel);
        $queryModels  = [];


        if ($models) {
            foreach ($models as $model) {

                $relatedInstance = $this->getRelationModel($objectModelName, $model);
                $relatedInstanceIds = $relatedInstance::where(strtolower($objectModelName).'_id', $queryModelId)
                    ->get()->pluck(strtolower($model).'_id')
                    ->toArray();
                $instance = app('App\Models\\'.$model);
                $searchModels = $instance::search($querySearch)
                    ->whereIn('id', $relatedInstanceIds)
                    ->get()
                    ->makeHidden(['created_at', 'updated_at'])
                    ->toArray();
                $queryModels = array_merge($searchModels, $queryModels);
            }
        }

        return $queryModels;

    }

    public function getRows (string $subjectModel, string $queryModel,  int $id): array
    {
        $objectModelName = $this->getModelName($queryModel);

        $model = $this->getRelationModel($objectModelName, $subjectModel);

        $model = $model::where(strtolower($subjectModel).'_id', $id)->first();
        $fields = $model->getAttributes();    // Получаем все атрибуты модели
        $casts = $model->getCasts();
        $table = $model->getTable();          // Получаем имя таблицы
        $columnTypes = [];

        foreach ($fields as $field => $value) {
            $type = $casts[$field] ?? Schema::getColumnType($table, $field); // Иначе получаем тип из схемы базы данных

            if ($model->getKeyName() !== $field && strtolower($subjectModel).'_id' !== $field &&  strtolower($objectModelName).'_id' !== $field) {
                $columnTypes[$field] = [
                    'type' => $type,
                    'value' => $value,
                    'required' => ''
                    ];
            }


        }

        return $columnTypes;// Массив для хранения типов полей

    }

    public function update(string $subjectModel, string $queryModel,  int $id, array $data)
    {
        $objectModelName = $this->getModelName($queryModel);

        $model = $this->getRelationModel($objectModelName, $subjectModel);

        return $model::where(strtolower($subjectModel).'_id', $id)->update($data);
    }

}
