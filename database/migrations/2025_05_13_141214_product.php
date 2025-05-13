<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key');
            $table->string('product_title');
            $table->text('product_description')->nullable();
            $table->string('style')->nullable();
            $table->string('sanmar_mainframe_color')->nullable();
            $table->string('size')->nullable();
            $table->string('color_name')->nullable();
            $table->decimal('piece_price', 8, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unique(['unique_key', 'user_id'], 'products_unique_key_user_id_unique');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};