<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Modify the grade column to allow specific values only
            $table->string('grade')->nullable()->change();
            
            // Add a check constraint for valid grades
            DB::statement("
                ALTER TABLE student_subjects
                ADD CONSTRAINT check_valid_grade
                CHECK (
                    grade IS NULL 
                    OR grade = 'INC'
                    OR (
                        CAST(grade AS DECIMAL(2,1)) BETWEEN 1.0 AND 5.0
                        AND grade REGEXP '^[1-5](\\.0|\\.[05])$'
                    )
                )
            ");
        });
    }

    public function down()
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Remove the check constraint
            DB::statement("ALTER TABLE student_subjects DROP CONSTRAINT check_valid_grade");
        });
    }
};
