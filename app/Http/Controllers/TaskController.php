<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
  use HttpResponses;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return TaskResource::collection(
      Task::where('user_id', Auth::user()->id)->get()
    );
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(StoreTaskRequest $request)
  {
    $request->validated($request->all());

    $task = Task::create([
      'user_id' => Auth::user()->id,
      'name' => $request->name,
      'description' => $request->description,
      'priority' => $request->priority,
    ]);

    return new TaskResource($task);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function show(Task $task)
  {
    return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TaskResource($task);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function edit(Task $task)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Task $task)
  {
    if (Auth::user()->id !== $task->user_id) {
      return $this->error('', 'You are not authorized to make this request', 403);
    }

    $task->update($request->all());

    return new TaskResource($task);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Task  $task
   * @return \Illuminate\Http\Response
   */
  public function destroy(Task $task)
  {
    return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();
  }

  private function isNotAuthorized($task)
  {
    if (Auth::user()->id !== $task->user_id) {
      return $this->error('', 'You are not authorized to make this request', 403);
    }
  }
}
