<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('category_id');
            $table->uuid('main_image_id')->nullable();
            $table->json('attributes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('main_image_id')->references('id')->on('images')->onDelete('set null');
        });

        Schema::create('image_product', function (Blueprint $table) {
            $table->uuid('image_id');
            $table->uuid('product_id');
            $table->primary(['image_id', 'product_id']);
            
            $table->integer('sort_order')->default(0);
       
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('image_product');
        Schema::dropIfExists('products');
    }
}