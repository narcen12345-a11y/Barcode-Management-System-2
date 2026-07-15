<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sites
        Schema::table('sites', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Material Types
        Schema::table('material_types', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Material Models
        Schema::table('material_models', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Materials
        Schema::table('materials', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_active');
        });

        // Barcodes
        Schema::table('barcodes', function (Blueprint $table) {
            $table->index('barcode_id');
            $table->index('serial_number');
            $table->index('status');
            $table->index('is_active');
            $table->index('site_id');
            $table->index('material_id');
            $table->index('created_at');
        });

        // Barcode Histories
        Schema::table('barcode_histories', function (Blueprint $table) {
            $table->index('barcode_id');
            $table->index('field_name');
            $table->index('changed_by');
            $table->index('created_at');
        });

        // Audit Logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('entity_type');
            $table->index('action');
            $table->index('created_at');
        });

        // Activity Logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('module');
            $table->index('activity');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('material_types', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('material_models', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('barcodes', function (Blueprint $table) {
            $table->dropIndex(['barcode_id']);
            $table->dropIndex(['serial_number']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['site_id']);
            $table->dropIndex(['material_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('barcode_histories', function (Blueprint $table) {
            $table->dropIndex(['barcode_id']);
            $table->dropIndex(['field_name']);
            $table->dropIndex(['changed_by']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['entity_type']);
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['module']);
            $table->dropIndex(['activity']);
            $table->dropIndex(['created_at']);
        });
    }
};
