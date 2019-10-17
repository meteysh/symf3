<?php


namespace App\TaskTrainer;

/**
 * Class TaskFactory
 * @package App\TaskTrainer
 */
class TaskFactory
{
    public static function createTask($task)
    {
        $task = ucfirst($task);
        $task = "App\TaskTrainer\Tasks\\" . $task .'\\'. $task;
        return new $task();
    }
}