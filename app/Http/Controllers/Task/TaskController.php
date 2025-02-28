<?php

namespace App\Http\Controllers\Task;

use Exception;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Api\Task\TaskIndexRequest;
use App\Http\Requests\Api\Task\TaskStoreRequest;
use App\Http\Requests\Api\Task\TaskUpdateRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskIndexRequest $request)
    {
        try {

            $user = $request->user();

            $base_query = $user
            ->tasks()
            ->when($request->search, function ($query) {
                $query->where(function($query) {
                    $search = request('search');
                    $query->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
                });
            })->when($request->filled('status'), function ($query) {
                $query->where(function($query){
                    $query->where('status', request('status'));
                });
            })->when($request->order_by == OLDEST, function($query) {
                $query->oldest('id');
            }, function($query) {
                $query->latest('id');
            });

            $data['total_tasks'] = $base_query->count();

            $data['tasks'] = TaskResource::collection($base_query->skip($request->skip)->take($request->take)->get());

            return $this->success('', 50101, $data);

        } catch(Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        try {

            $user = $request->user();

            $validated = $request->validated();

            $data['task'] = DB::transaction(function() use($user, $validated) {

                $task = $user->tasks()->create($validated);

                throw_if(! $task, new Exception(__('messages.task_creation_failed'), 60001));

                return new TaskResource($task->refresh());
            });

           return $this->success(__('messages.task_created'), 60101, $data);

        } catch(Exception $e) {

            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {   
        Gate::allowIf(fn (User $user) => $user->id == $task->user_id);

        return $this->success('', '', [
            'task' => new TaskResource($task) 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        Gate::allowIf(fn (User $user) => $user->id == $task->user_id);

        try {

            $validated = $request->validated();

            $data['task'] = DB::transaction(function() use($task, $validated) {

                $result = $task->update($validated);

                throw_if(! $result, new Exception(__('messages.task_updation_failed'), 60002));

                return new TaskResource($task->refresh());
            });

           return $this->success(__('messages.task_updated'), 60102, $data);

        } catch(Exception $e) {

            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {   
        Gate::allowIf(fn (User $user) => $user->id == $task->user_id);

        try {

            DB::transaction(function() use($task) {

                throw_if(! $task->delete(), new Exception(__('messages.task_deletion_failed'), 60003));
            });
    
            return $this->success(__('messages.task_deleted'), 60103, [
                'task' => new TaskResource($task) 
            ]);

        } catch(Exception $e) {

            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
