<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->onDelete('cascade');
            $table->date('date');
            $table->string('progress')->nullable();
            $table->enum('level', ['Level 1', 'Level 2', 'Level 3'])->nullable();
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
            $table->unique(['child_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress');
    }
}; 