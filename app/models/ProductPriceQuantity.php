<?php

class ProductPriceQuantity extends Eloquent
{
    protected $table = 'jocom_product_price_quantity';

    protected $fillable = ['price_id', 'original_qty', 'status'];
}

