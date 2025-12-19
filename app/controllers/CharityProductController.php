<?php

class CharityProductController extends BaseController
{
    public function show($charityId)
    {
        $charityCategory = CharityCategory::find($charityId);

        if ( ! $charityCategory) {
            return Redirect::to('product/category');
        }

        $charityProducts = Product::select([
            'jocom_charity_product.id as id',
            'jocom_products.name as name',
            'jocom_products.sku as sku',
            'jocom_product_price.label as label',
            'jocom_charity_product.qty as qty',
            'jocom_charity_product.quota as quota',
        ])
            ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->leftJoin('jocom_charity_product', 'jocom_product_price.id', '=', 'jocom_charity_product.product_price_id')
            ->where('jocom_charity_product.charity_id', '=', $charityId)
            ->get();

        return View::make('product.category.charity_product.show', [
            'charityCategory' => $charityCategory,
            'charityProducts' => $charityProducts,
        ]);
    }

    public function update($charityId)
    {
        $charityProductId = Input::get('charityProductId');
        $charityProduct   = CharityProduct::find($charityProductId);
        $quantity         = Input::get('quantity');
        $quota            = Input::get('quota');

        if ($quantity >= 0) {
            $charityProduct->qty = $quantity;
        }

        if ($quota > 0) {
            $charityProduct->quota = $quota;
        }

        $charityProduct->save();

        return Redirect::back();
    }

    public function destroy($charityId)
    {
        $charityProductId = Input::get('charityProductId');
        $charityProduct   = CharityProduct::find($charityProductId);
        $labelId          = $charityProduct->product_price_id;
        $price            = Price::find($labelId);
        $product          = Product::find($price->product_id);
        $categories       = explode(',', $product->category);
        $category         = CharityCategory::find($charityId);
        $categoryId       = $category->category_id;

        if (in_array($categoryId, $categories)) {
            $categories        = array_diff($categories, [$categoryId]);
            $product->category = implode(',', $categories);
            $product->save();
        }

        $productsCategoryMatch = new ProductsCategory;
        $productsCategoryMatch = $productsCategoryMatch->findMatch($product->id, $categoryId);

        if ($productsCategoryMatch) {
            $productsCategoryMatch->delete();

            $count = ProductsCategory::where('product_id', '=', $product->id)->count();

            if ($count == 0) {
                $productsCategory              = new ProductsCategory;
                $productsCategory->product_id  = $product->id;
                $productsCategory->category_id = 0;
                $productsCategory->main        = 1;
                $productsCategory->save();
            }
        }

        $charityProduct->delete();

        return Redirect::back();
    }

    public function getAdd($charityId)
    {
        return View::make('product.category.charity_product.add', ['charityId' => $charityId]);
    }

    public function getAddoption($charityId)
    {
        $charityId = Input::get('charityId');
        $productId = Input::get('productId');

        return View::make('product.category.charity_product.addoption', [
            'charityId' => $charityId,
            'productId' => $productId,
        ]);
    }

    public function postAddoption($charityId)
    {
        $quota   = Input::get('quota');
        $labelId = Input::get('labelId');

        $charityProduct = CharityProduct::firstOrNew([
            'product_price_id' => $labelId,
            'charity_id'       => $charityId,
        ]);

        $charityProduct->product_price_id = $labelId;
        $charityProduct->qty              = $quota;
        $charityProduct->quota            = $quota;
        $charityProduct->charity_id       = $charityId;
        $charityProduct->save();

        $price      = Price::find($labelId);
        $product    = Product::find($price->product_id);
        $categories = explode(',', $product->category);
        $category   = CharityCategory::find($charityId);
        $categoryId = $category->category_id;

        if ( ! in_array($categoryId, $categories)) {
            $categories[]      = $categoryId;
            $product->category = implode(',', $categories);
            $product->save();
        }

        $productsCategoryMatch = new ProductsCategory;
        $parentCategory        = $productsCategoryMatch->findMatch($product->id, 0);

        if ($parentCategory) {
            $parentCategory->delete();
        }

        $productsCategoryMatch = $productsCategoryMatch->findMatch($product->id, $categoryId);

        if ( ! $productsCategoryMatch) {
            $count = ProductsCategory::where('product_id', '=', $product->id)->count();

            $productsCategory              = new ProductsCategory;
            $productsCategory->product_id  = $product->id;
            $productsCategory->category_id = $categoryId;
            $productsCategory->main        = ($count > 0) ? 0 : 1;
            $productsCategory->save();
        }

        return View::make('product.category.charity_product.close');
    }

    public function getDatatable()
    {
        $products = Product::select([
            'jocom_products.id as id',
            'jocom_products.sku as sku',
            'jocom_seller.company_name as company_name',
            'jocom_products.name as name',
            'jocom_products_category.category_name as category_name',
            'jocom_products.status as status',
        ])
            ->leftJoin('jocom_seller', 'jocom_products.sell_id', '=', 'jocom_seller.id')
            ->leftJoin('jocom_products_category', 'jocom_products.category', '=', 'jocom_products_category.id')
            ->where('jocom_products.status', '!=', 2);

        return Datatables::of($products)
            ->edit_column('status', function ($row) {
                switch ($row->status) {
                    case 1:
                        return '<span class="label label-success">Active</span>';
                        break;
                    case 2:
                        return '<span class="label label-danger">Deleted / Archived</span>';
                        break;
                    default:
                        return '<span class="label label-warning">Inactive</span>';
                        break;
                }
            })
            ->add_column('Action', function ($row) {
                return '<button class="btn btn-primary" name="productId" value="'.$row->id.'">Select</button>';
            })
            ->make();
    }

    public function getProductLabelDatatable($productId)
    {
        $labels = Product::select([
            'jocom_product_price.id as id',
            'jocom_product_price.label as label',
            'jocom_product_price.price as price',
            'jocom_product_price.price_promo as price_promo',
        ])
            ->leftJoin('jocom_product_price', 'jocom_products.id', '=', 'jocom_product_price.product_id')
            ->where('jocom_products.id', '=', $productId)
            ->where('jocom_product_price.status', '=', 1);

        return Datatables::of($labels)
            ->add_column('Action', function ($row) {
                return '<input class="form-control" type="text" name="quota" placeholder="Quota" required> <button class="btn btn-primary" name="labelId" value="'.$row->id.'">Add</button>';
            })
            ->make();
    }
}
