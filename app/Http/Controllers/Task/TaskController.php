<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task\Task;
use App\Models\Task\TaskUser;
use App\Models\Task\TaskVariant;
use App\Models\Task\TaskVariantValue;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function super_admin_get_all()
    {
        $tasks = Task::orderBy('id', 'DESC')->with(['variants'])->paginate(20);
        return $tasks;
    }
    public function get_all_employee_task()
    {
        $query = Task::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('task_users')
                ->where('task_users.user_id', auth()->id())
                ->whereColumn('task_users.task_id', 'tasks.id');
        })
            ->orderBy('id', 'DESC')
            ->with(['variants','auth_user']);

        if (request()->has('date')) {
            $query->whereDate('target_date', request()->date);
        }

        $tasks = $query->paginate(25);
        return $tasks;
    }

    public function get_task(Task $task)
    {
        $task->variants =  $task->variants()->get();
        $task->users =  $task->users()->get();

        return response()->json($task);
    }

    public function complete(Task $task)
    {
        if ($task->complete) {
            $task->complete = 0;
        } else {
            $task->complete = 1;
        }
        $task->save();

        return response()->json($task);
    }

    public function complete_by_employee(TaskUser $task)
    {
        if ($task->is_complete) {
            $task->is_complete = 0;
        } else {
            $task->is_complete = 1;
        }
        $task->save();

        return response()->json($task);
    }

    public function blink(Task $task)
    {
        if ($task->is_blink) {
            $task->is_blink = 0;
        } else {
            $task->is_blink = 1;
        }
        $task->save();

        return response()->json($task);
    }

    public function incomplete_task_count()
    {
        $task = Task::where('complete', 0)->count();
        return response()->json($task);
    }

    public function varients()
    {
        $varients = TaskVariant::with(['values'])->get();
        return $varients;
    }

    public function save_new_varient()
    {
        if (request()->title) {
            $varient = TaskVariant::create([
                'title' => request()->title,
                'creator' => auth()->user()->id,
            ]);
            return $varient;
        }
    }

    public function save_varient_values()
    {
        $task_variant_id = null;
        $ids = [];
        foreach (request()->varient_values as $item) {
            $item = (object) $item;

            $task_variant_id = $item->task_variant_id;
            if (isset($item->id)) {
                $varient_value = TaskVariantValue::find($item->id);
                if ($varient_value) {
                    $varient_value->title = $item->title;
                    $varient_value->save();
                    $ids[] = $varient_value->id;
                }
            } else {
                $varient_value = TaskVariantValue::create([
                    "title" => $item->title,
                    "task_variant_title" => $item->task_variant_title,
                    "task_variant_id" => $item->task_variant_id,
                    "creator" => auth()->user()->id,
                ]);
                $ids[] = $varient_value->id;
            }
        }

        TaskVariantValue::where('task_variant_id', $task_variant_id)
            ->whereNotIn('id', $ids)
            ->where('creator', auth()->user()->id)
            ->delete();
        return response()->json('success');
    }


    public function save_new_task()
    {

        $validator = Validator::make(request()->all(), [
            "title" => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'err_message' => 'validation error',
                'data' => $errors,
            ], 422);
        }


        if ($task = Task::create(request()->all())) {
            $task->creator = auth()->user()->id;
            $task->save();
            $userRoles = auth()->user()->roles()->first();
            if ($userRoles !== 1) {
                $taskUser = new TaskUser();
                $taskUser->task_id = $task->id;
                $taskUser->user_id = auth()->id();
                $taskUser->save();
            }
        }
        return response()->json('success');
    }

    public function update()
    {
        $validator = Validator::make(request()->all(), [
            "title" => 'required',
            "id" => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'err_message' => 'validation error',
                'data' => $errors,
            ], 422);
        }

        $task = Task::find(request()->id);
        $task->fill(request()->except(['user_id', 'varients', 'id']))->save();
        $task->users()->sync(request()->user_id);
        $task->variants()->sync(array_values(array_filter(request()->varients)));

        return $task;
    }

    public function employeeUpdate()
    {
        $validator = Validator::make(request()->all(), [
            "title" => 'required',
            "id" => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'err_message' => 'validation error',
                'data' => $errors,
            ], 422);
        }

        $task = Task::find(request()->id);
        $task->fill(request()->except(['user_id', 'varients', 'id']))->save();
        $task->variants()->sync(array_values(array_filter(request()->varients)));

        return $task;
    }

    public function delete()
    {
        $task = Task::find(request()->id);
        if ($task) {
            $task->delete();
            return true;
        }
        return false;
    }

    public function all()
    {
        $paginate = (int) request()->paginate ?? 10;
        $orderBy = request()->orderBy ?? 'id';
        $orderByType = request()->orderByType ?? 'ASC';

        $status = 1;
        if (request()->has('status')) {
            $status = request()->status;
        }

        $query = Task::orderBy($orderBy, $orderByType);

        if (request()->has('target_date')) {
            $query->whereDate('target_date', request()->target_date);
        }

        if (request()->has('search_key')) {
            $key = request()->search_key;
            $query->where(function ($q) use ($key) {
                return $q->where('title', $key)
                    ->orWhere('details', $key)
                    ->orWhere('complete', $key)
                    ->orWhere('title', 'LIKE', '%' . $key . '%')
                    ->orWhere('details', 'LIKE', '%' . $key . '%')
                    ->orWhere('complete', 'LIKE', '%' . $key . '%');
            });
        }

        if (request()->has('user_id')) {
            $user_id = request()->user_id;
            $query->whereExists(function ($query) use ($user_id) {
                $query->select(DB::raw(1))
                    ->from('task_users')
                    ->where('task_users.user_id', $user_id)
                    ->whereColumn('task_users.task_id', 'tasks.id');
            });
            $query->with([
                'given_user' => function($q) use ($user_id){
                    $q->where('user_id', $user_id);
                },
            ]);
        }

        $data = $query->paginate($paginate);
        return response()->json($data);
    }
}
