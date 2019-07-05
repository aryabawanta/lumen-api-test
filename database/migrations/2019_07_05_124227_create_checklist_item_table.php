<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('checklist_id');
            $table->string('description');
            $table->integer('urgency');
            $table->string('due')->nullable();
            $table->boolean('is_completed')->default(0);
            $table->string('completed_at')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_item');
    }
}
