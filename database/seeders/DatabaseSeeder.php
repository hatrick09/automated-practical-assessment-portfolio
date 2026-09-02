<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Programme;
use App\Models\Rubric;
use App\Models\Semester;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Academic year/semester ---
        $year = AcademicYear::create(['name' => '2025/2026', 'is_current' => true]);
        $sem1 = $year->semesters()->create(['name' => 'Semester 1', 'is_current' => false]);
        $sem2 = $year->semesters()->create(['name' => 'Semester 2', 'is_current' => true]);

        // --- Admin ---
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@tvet.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ============================================================
        // DEPARTMENT 1: ENGINEERING
        // ============================================================
        $engineeringDept = Department::create(['name' => 'Engineering']);
        $electricalProgramme = Programme::create(['department_id' => $engineeringDept->id, 'name' => 'Electrical Engineering']);
        $automotiveProgramme = Programme::create(['department_id' => $engineeringDept->id, 'name' => 'Automobile Engineering']);

        $electricalTrade = Trade::create(['trade_name' => 'Electrical Installation', 'programme_id' => $electricalProgramme->id]);
        $automotiveTrade = Trade::create(['trade_name' => 'Automotive Technology', 'programme_id' => $automotiveProgramme->id]);

        $courseWiring = Course::create(['trade_id' => $electricalTrade->id, 'course_name' => 'Domestic Wiring', 'course_code' => 'EE201', 'credit_hours' => 3]);
        $courseMotorControl = Course::create(['trade_id' => $electricalTrade->id, 'course_name' => 'Motor Control Systems', 'course_code' => 'EE305', 'credit_hours' => 4]);
        $courseEngine = Course::create(['trade_id' => $automotiveTrade->id, 'course_name' => 'Engine Fundamentals', 'course_code' => 'AE110', 'credit_hours' => 3]);

        $engRubricsData = [
            [$courseWiring->id, 'Correct circuit diagram interpretation', 20],
            [$courseWiring->id, 'Safe use of hand tools', 15],
            [$courseWiring->id, 'Cable termination quality', 20],
            [$courseWiring->id, 'Adherence to safety protocol', 15],
            [$courseMotorControl->id, 'Motor starter wiring accuracy', 25],
            [$courseMotorControl->id, 'Fault diagnosis speed & accuracy', 20],
            [$courseMotorControl->id, 'Control panel labelling', 10],
            [$courseEngine->id, 'Engine disassembly technique', 20],
            [$courseEngine->id, 'Component identification', 15],
            [$courseEngine->id, 'Reassembly & torque specification', 25],
        ];

        $hodEngineering = User::create([
            'name' => 'Instructor Kwame Mensah', 'email' => 'instructor1@tvet.test',
            'password' => Hash::make('password'), 'role' => 'instructor',
            'department_id' => $engineeringDept->id, 'is_hod' => true,
        ]);
        $hodEngineering->assignedCourses()->sync([$courseWiring->id, $courseMotorControl->id, $courseEngine->id]);

        // ============================================================
        // DEPARTMENT 2: INFORMATION & COMMUNICATION TECHNOLOGY
        // ============================================================
        $ictDept = Department::create(['name' => 'Information & Communication Technology']);
        $itProgramme = Programme::create(['department_id' => $ictDept->id, 'name' => 'Information Technology']);
        $itTrade = Trade::create(['trade_name' => 'Software Development', 'programme_id' => $itProgramme->id]);

        $courseProgramming = Course::create(['trade_id' => $itTrade->id, 'course_name' => 'Programming Fundamentals', 'course_code' => 'IT101', 'credit_hours' => 3]);
        $courseWebDev = Course::create(['trade_id' => $itTrade->id, 'course_name' => 'Web Development', 'course_code' => 'IT210', 'credit_hours' => 3]);

        $ictRubricsData = [
            [$courseProgramming->id, 'Correct syntax and program logic', 20],
            [$courseProgramming->id, 'Code documentation and comments', 10],
            [$courseProgramming->id, 'Program runs without errors', 20],
            [$courseWebDev->id, 'HTML/CSS structure and validity', 15],
            [$courseWebDev->id, 'Responsive design implementation', 15],
            [$courseWebDev->id, 'JavaScript functionality', 20],
        ];

        $hodIct = User::create([
            'name' => 'Instructor Ama Owusu', 'email' => 'instructor2@tvet.test',
            'password' => Hash::make('password'), 'role' => 'instructor',
            'department_id' => $ictDept->id, 'is_hod' => true,
        ]);
        $hodIct->assignedCourses()->sync([$courseProgramming->id, $courseWebDev->id]);

        // ============================================================
        // DEPARTMENT 3: FASHION DESIGN
        // ============================================================
        $fashionDept = Department::create(['name' => 'Fashion Design']);
        $fashionProgramme = Programme::create(['department_id' => $fashionDept->id, 'name' => 'Fashion Design']);
        $fashionTrade = Trade::create(['trade_name' => 'Garment Construction', 'programme_id' => $fashionProgramme->id]);

        $coursePattern = Course::create(['trade_id' => $fashionTrade->id, 'course_name' => 'Pattern Drafting', 'course_code' => 'FD101', 'credit_hours' => 3]);
        $courseGarment = Course::create(['trade_id' => $fashionTrade->id, 'course_name' => 'Garment Construction', 'course_code' => 'FD210', 'credit_hours' => 4]);

        $fashionRubricsData = [
            [$coursePattern->id, 'Accuracy of body measurements', 20],
            [$coursePattern->id, 'Pattern grading technique', 15],
            [$courseGarment->id, 'Stitching quality and consistency', 20],
            [$courseGarment->id, 'Seam finishing', 15],
            [$courseGarment->id, 'Overall garment fit', 15],
        ];

        $hodFashion = User::create([
            'name' => 'Instructor John Boateng', 'email' => 'instructor3@tvet.test',
            'password' => Hash::make('password'), 'role' => 'instructor',
            'department_id' => $fashionDept->id, 'is_hod' => true,
        ]);
        $hodFashion->assignedCourses()->sync([$coursePattern->id, $courseGarment->id]);

        // --- Rubrics (all departments) ---
        $rubrics = collect(array_merge($engRubricsData, $ictRubricsData, $fashionRubricsData))
            ->map(fn ($r) => Rubric::create(['course_id' => $r[0], 'criterion' => $r[1], 'max_score' => $r[2]]));

        // ============================================================
        // STUDENTS — spread across the 3 departments
        // ============================================================
        $engineeringNames = ['Kojo Asante', 'Abena Darko', 'Yaw Appiah', 'Efua Sarpong', 'Kwabena Owusu', 'Adjoa Frimpong'];
        $ictNames = ['Kofi Adjei', 'Akosua Nyarko', 'Kwesi Bonsu', 'Afia Danso', 'Yaa Konadu'];
        $fashionNames = ['Kwaku Tetteh', 'Esi Amoah', 'Fiifi Arthur', 'Nana Yeboah'];

        $genders = ['male', 'female'];
        $studentIndex = 0;
        $makeStudents = function (array $names, Department $dept) use (&$studentIndex, $genders, $year) {
            return collect($names)->map(function ($name) use (&$studentIndex, $dept, $genders, $year) {
                $studentIndex++;
                return User::create([
                    'name' => $name,
                    'email' => 'student'.$studentIndex.'@tvet.test',
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'department_id' => $dept->id,
                    'level' => 'Level '.(200 + ($studentIndex % 3) * 100),
                    'gender' => $genders[$studentIndex % 2],
                    'student_number' => 'TVET/'.str_replace('/', '', $year->name).'/'.str_pad($studentIndex, 4, '0', STR_PAD_LEFT),
                ]);
            });
        };

        $engineeringStudents = $makeStudents($engineeringNames, $engineeringDept);
        $ictStudents = $makeStudents($ictNames, $ictDept);
        $fashionStudents = $makeStudents($fashionNames, $fashionDept);

        // --- Enrollments (with a starting attendance %) ---
        foreach ($engineeringStudents as $i => $s) {
            $s->enrolledCourses()->sync([
                $courseWiring->id => ['attendance_percentage' => random_int(70, 100)],
                $courseMotorControl->id => ['attendance_percentage' => random_int(70, 100)],
            ]);
            if ($i % 2 === 0) {
                $s->enrolledCourses()->syncWithoutDetaching([$courseEngine->id => ['attendance_percentage' => random_int(70, 100)]]);
            }
        }
        foreach ($ictStudents as $s) {
            $s->enrolledCourses()->sync([
                $courseProgramming->id => ['attendance_percentage' => random_int(70, 100)],
                $courseWebDev->id => ['attendance_percentage' => random_int(70, 100)],
            ]);
        }
        foreach ($fashionStudents as $s) {
            $s->enrolledCourses()->sync([
                $coursePattern->id => ['attendance_percentage' => random_int(70, 100)],
                $courseGarment->id => ['attendance_percentage' => random_int(70, 100)],
            ]);
        }

        // ============================================================
        // Sample assessments (mostly pre-approved, a few left pending)
        // ============================================================
        $count = 0;
        $seedAssessments = function ($students, $rubrics, $instructor, $hod, $howManyRubricsEach) use (&$count, $sem2) {
            foreach ($students as $student) {
                foreach ($rubrics->take($howManyRubricsEach) as $rubric) {
                    Assessment::create([
                        'student_id' => $student->id,
                        'rubric_id' => $rubric->id,
                        'instructor_id' => $instructor->id,
                        'score' => max(1, $rubric->max_score - random_int(0, (int) ($rubric->max_score * 0.3))),
                        'remarks' => 'Good practical demonstration of the task.',
                        'date' => now()->subDays(random_int(1, 45)),
                        'semester_id' => $sem2->id,
                        'status' => 'approved',
                        'reviewed_by' => $hod->id,
                        'reviewed_at' => now()->subDays(random_int(1, 20)),
                    ]);
                    $count++;
                }
            }
        };

        $wiringRubrics = $rubrics->filter(fn ($r) => $r->course_id === $courseWiring->id)->values();
        $motorRubrics = $rubrics->filter(fn ($r) => $r->course_id === $courseMotorControl->id)->values();
        $programmingRubrics = $rubrics->filter(fn ($r) => $r->course_id === $courseProgramming->id)->values();
        $webDevRubrics = $rubrics->filter(fn ($r) => $r->course_id === $courseWebDev->id)->values();
        $patternRubrics = $rubrics->filter(fn ($r) => $r->course_id === $coursePattern->id)->values();
        $garmentRubrics = $rubrics->filter(fn ($r) => $r->course_id === $courseGarment->id)->values();

        $seedAssessments($engineeringStudents->take(5), $wiringRubrics, $hodEngineering, $hodEngineering, 3);
        $seedAssessments($engineeringStudents->take(4), $motorRubrics, $hodEngineering, $hodEngineering, 1);
        $seedAssessments($ictStudents, $programmingRubrics, $hodIct, $hodIct, 2);
        $seedAssessments($ictStudents->take(3), $webDevRubrics, $hodIct, $hodIct, 2);
        $seedAssessments($fashionStudents, $patternRubrics, $hodFashion, $hodFashion, 2);
        $seedAssessments($fashionStudents->take(3), $garmentRubrics, $hodFashion, $hodFashion, 2);

        // A few left pending in different departments, so each HOD's approvals queue has work.
        Assessment::create([
            'student_id' => $engineeringStudents->first()->id, 'rubric_id' => $wiringRubrics->last()->id,
            'instructor_id' => $hodEngineering->id, 'score' => $wiringRubrics->last()->max_score - 2,
            'remarks' => 'Awaiting HOD sign-off.', 'date' => now()->subDay(),
            'semester_id' => $sem2->id, 'status' => 'pending',
        ]);
        Assessment::create([
            'student_id' => $ictStudents->first()->id, 'rubric_id' => $webDevRubrics->last()->id,
            'instructor_id' => $hodIct->id, 'score' => $webDevRubrics->last()->max_score - 3,
            'remarks' => 'Awaiting HOD sign-off.', 'date' => now()->subDay(),
            'semester_id' => $sem2->id, 'status' => 'pending',
        ]);
        Assessment::create([
            'student_id' => $fashionStudents->first()->id, 'rubric_id' => $garmentRubrics->last()->id,
            'instructor_id' => $hodFashion->id, 'score' => $garmentRubrics->last()->max_score - 1,
            'remarks' => 'Awaiting HOD sign-off.', 'date' => now()->subDay(),
            'semester_id' => $sem2->id, 'status' => 'pending',
        ]);
        $count += 3;

        $this->command?->info("Seeded {$count} assessments across 3 departments.");
        $this->command?->info('Logins (password: "password"):');
        $this->command?->info('  Admin: admin@tvet.test');
        $this->command?->info('  Engineering HOD: instructor1@tvet.test | ICT HOD: instructor2@tvet.test | Fashion HOD: instructor3@tvet.test');
        $this->command?->info('  Engineering students: student1-6 | ICT students: student7-11 | Fashion students: student12-15');
    }
}
