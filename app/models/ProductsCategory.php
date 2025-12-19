<?php

class ProductsCategory extends Eloquent
{
    /**
     * Caution: Not to be confused with table `jocom_products_category` or Category model
     */
    protected $table = 'jocom_categories';

    public function setUpdatedAtAttribute($value)
    {
        // Disable Eloquent default `updated_at` column in DB
    }

    public function findMatch($productId, $categoryId)
    {
        return $this->where('product_id', '=', $productId)
            ->where('category_id', '=', $categoryId)
            ->first();
    }

    public function getByProduct($productId)
    {
        return $this->where('product_id', '=', $productId)->get();
    }

    public function getMainCategory($productId)
    {
        return $this->where('product_id', '=', $productId)
            ->where('main', '=', 1)
            ->first();
    }

    public function setMainCategory($productId, $categoryId)
    {
        $this->unsetMainCategory($productId);

        DB::table($this->table)
            ->where('product_id', '=', $productId)
            ->where('category_id', '=', $categoryId)
            ->update(['main' => 1]);
    }

    private function unsetMainCategory($productId)
    {
        DB::table($this->table)
            ->where('product_id', '=', $productId)
            ->update(['main' => 0]);
    }
}
