<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_definition_id')->constrained()->cascadeOnDelete();
            $table->string('field_key');
            $table->json('label');
            $table->string('type')->default('text'); // text, email, tel, textarea, select, checkbox, radio, date, file, tags, url, relation_select
            $table->json('options')->nullable(); // static options for select/radio
            $table->string('relation_source')->nullable(); // FQCN model class, for relation_select
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable(); // extra Laravel validation rule strings
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_system')->default(false); // brief-specified default field, protected from deletion
            $table->string('maps_to_column')->nullable(); // when set, the value is written to this real column instead of field_values JSON
            $table->boolean('is_filterable')->default(false); // promotes a JSON custom field to an indexed generated column
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['form_definition_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
