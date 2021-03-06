<?php

namespace C2Y\Http\Controllers\API;

use C2Y\Http\Controllers\API\APIController;
use C2Y\User;
use C2Y\Bug;
use C2Y\Lesson;
use C2Y\Course;
use C2Y\Comment;
use Illuminate\Http\Request;
use C2Y\Http\Controllers\Controller;

class CustomController extends Controller {
    public function me($user) {
        try {
            $lessons = Lesson::me($user);
            $courses = Course::me($user);
            return APIController::success([
                'lessons' => $lessons,
                'courses' => $courses
            ]);
        } catch (Exception $e) {
            return APIController::error($e->getMessage());
        }
    }

    public function user ($id) {
        $user = User::get($id);
        $arr = $user->toArray();
        $arr['classrooms'] = $user->classrooms->toArray();
        return APIController::success(['user' => $user]);
    }

    public function notification ($user) {
        try {
            $comments = Comment::getComments($user);
            return APIController::success([
                'comments' => $comments
            ]);
        } catch (Exception $e) {
            return APIController::error($e->getMessage(), 403);
        }
    }

    public function bug (Request $request) {
        $json = json_encode($request->all());
        return APIController::success(['bug' => Bug::create([
            'bug' => $json
        ])]);
    }

    public function updateUser (Request $request, $id) {
        $user = User::find($id);
        if (!$user) {
            return APIController::error('User not found', 403);
        }
        $user->fill($request->all());
        $user->save();
    }
}
