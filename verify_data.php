<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

echo "=== VERIFYING DATA CONSISTENCY ===\n\n";

// 1. Check Users (Guru)
echo "1. USERS (role=guru):\n";
echo str_repeat("-", 80) . "\n";
$users = User::where('role', 'guru')->with('teacher')->orderBy('name')->get();
foreach ($users as $user) {
    $teacherId = $user->teacher ? $user->teacher->id : 'NO TEACHER RECORD';
    $employeeCode = $user->teacher ? $user->teacher->employee_code : 'N/A';
    echo "User ID: {$user->id} | Name: {$user->name} | Teacher ID: {$teacherId} | Employee Code: {$employeeCode}\n";
}

echo "\n2. TEACHERS TABLE:\n";
echo str_repeat("-", 80) . "\n";
$teachers = Teacher::orderBy('name')->get();
foreach ($teachers as $teacher) {
    echo "ID: {$teacher->id} | Name: {$teacher->name} | User ID: {$teacher->user_id} | Employee Code: {$teacher->employee_code}\n";
}

echo "\n3. SUBJECT-TEACHER RELATIONSHIPS:\n";
echo str_repeat("-", 80) . "\n";
$subjects = Subject::with('teachers')->orderBy('name')->get();
foreach ($subjects as $subject) {
    echo "\n{$subject->name}:\n";
    if ($subject->teachers->count() > 0) {
        foreach ($subject->teachers as $teacher) {
            echo "  - Teacher ID: {$teacher->id}, Name: {$teacher->name}\n";
        }
    } else {
        echo "  (No teachers)\n";
    }
}

echo "\n4. CHECKING FOR ORPHAN TEACHERS (Teacher without User):\n";
echo str_repeat("-", 80) . "\n";
$orphans = Teacher::whereDoesntHave('user')->get();
if ($orphans->count() > 0) {
    echo "⚠ Found {$orphans->count()} orphan teachers:\n";
    foreach ($orphans as $orphan) {
        echo "  - ID: {$orphan->id}, Name: {$orphan->name}, User ID: {$orphan->user_id}\n";
    }
} else {
    echo "✓ No orphan teachers found\n";
}

echo "\n5. CHECKING PIVOT TABLE DIRECTLY:\n";
echo str_repeat("-", 80) . "\n";
$pivots = DB::table('subject_teacher')
    ->join('subjects', 'subject_teacher.subject_id', '=', 'subjects.id')
    ->join('teachers', 'subject_teacher.teacher_id', '=', 'teachers.id')
    ->select('subjects.name as subject', 'teachers.id as teacher_id', 'teachers.name as teacher')
    ->orderBy('subjects.name')
    ->get();

foreach ($pivots as $pivot) {
    echo "{$pivot->subject} -> Teacher ID: {$pivot->teacher_id}, Name: {$pivot->teacher}\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total Users (guru): {$users->count()}\n";
echo "Total Teachers: {$teachers->count()}\n";
echo "Total Pivot Records: {$pivots->count()}\n";
