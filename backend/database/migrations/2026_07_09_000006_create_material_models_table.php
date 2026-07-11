<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_type_id')->constrained('material_types')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['material_type_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_models');
    }
};
