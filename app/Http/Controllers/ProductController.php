<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images', 'mainImage'])->orderBy('sort_order')->get();
        return response()->json($products);
    }

    public function category($id)
    {
        $products = Product::where('category_id', $id)
            ->with(['category', 'images', 'mainImage'])
            ->orderBy('sort_order')
            ->get();
        return response()->json($products);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->all();

        if (!$request->has('main_image_id') && $request->has('images')) {
            $data['main_image_id'] = $data['images'][0];
        }

        $product = Product::create($data);

        if ($request->has('images')) {
            if (!$request->has('main_image_id')) {
                $data['main_image_id'] = $data['images'][0];
            }
            
            $this->syncImages($product, $request->images);
        }
        $product = $product->load(['category', 'images', 'mainImage']);

        Log::info('Stored Product:', $product->toArray());

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'images', 'mainImage']));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->except('sort_order');

        if (empty($data['main_image_id']) && $request->has('images')) {
            $data['main_image_id'] = $request->images[0];
        }

        if ($request->has('sort_order')) {
            $product->updateSortOrder($request->input('sort_order'));
        }
        $product->update($data);

        if ($request->has('images')) {
            $this->syncImages($product, $request->images);
        }
        $product = $product->load(['category', 'images', 'mainImage']);

        return response()->json($product);
    }

    private function syncImages(Product $product, array $images)
    {
        $images = collect($images)->mapWithKeys(function ($imageId, $index) {
            return [$imageId => ['sort_order' => $index+1]];
        });
        $product->images()->sync($images);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}