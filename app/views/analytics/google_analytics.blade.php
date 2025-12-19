<?php
$test = Config::get('constants.ENVIRONMENT');
if ($test != 'test')
{
?>
<!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-49935530-1', 'auto');


ga('require', 'ecommerce');

<?php
if (isset($id))
{
    // Transaction Data
    $trans = Transaction::find($id);

    $coupon = DB::table('jocom_transaction AS a')
    ->select('b.coupon_amount')
    ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
    ->where('a.id', '=', $id)
    ->first();

    $point = DB::table('jocom_transaction AS a')
    ->leftJoin('jocom_transaction_point AS b','a.id', '=', 'b.transaction_id')
    ->where('a.id', '=', $id)
    ->where('b.status', '=', '1')
    ->sum('b.amount');

    $total = $trans->total_amount - $coupon->coupon_amount - $point + $trans->gst_total;

    $trans = array('id'=>$id, 'revenue'=>$total, 'shipping'=>$trans->delivery_charges, 'tax'=>$trans->gst_total);


    // List of Items Purchased.
    $transD = DB::table('jocom_transaction_details AS a')
    ->leftJoin('jocom_products AS b', 'b.id', '=', 'a.product_id')
    // ->leftJoin('jocom_products_category AS c', 'c.id', '=', 'a.product_id')
    ->leftJoin('jocom_categories AS c', 'c.product_id', '=', 'a.product_id')
    ->leftJoin('jocom_products_category AS d', 'c.category_id', '=', 'd.id')
    ->where('a.transaction_id', '=', $id)
    // ->where('c.main', '=', 1)
    ->groupBy('a.id')
    ->get();

    foreach ($transD as $details)
    {
        $pro_name = $details->name . '/' . $details->price_label;
        $pro_price = $details->total - $details->disc + $details->gst_amount;
        $pro_price = $pro_price/$details->unit;

        $items[] = array('sku'=>$details->sku, 'name'=>$pro_name, 'category'=>$details->category_name, 'price'=>$pro_price, 'quantity'=>$details->unit);
    }

    // echo "<pre>";
    // var_dump($trans); echo "<br>";
    // var_dump($items); echo "<br>";

}
?>

ga('ecommerce:addTransaction', {
  'id': <?php echo "'" . $trans['id'] . "'" ?>,
  'revenue': <?php echo "'" . $trans['revenue'] . "'" ?>,
  'shipping': <?php echo "'" . $trans['shipping'] . "'" ?>,
  'tax': <?php echo "'" . $trans['tax'] . "'" ?>
});

<?php
foreach ($items as $item)
{
?>

ga('ecommerce:addItem', {
  'id': <?php echo "'" . $trans['id'] . "'" ?>,
  'name': <?php echo "'" . str_replace("'", "",$item['name']) . "'" ?>,
  'sku': <?php echo "'" . $item['sku'] . "'" ?>,
  'category': <?php echo "'" . str_replace("'", "",$item['category']) . "'" ?>,
  'price': <?php echo "'" . $item['price'] . "'" ?>,
  'quantity': <?php echo "'" . $item['quantity'] . "'" ?>
});

<?php
}
?>

ga('ecommerce:send');

ga('send', 'pageview');

// clear cart
// ga('ecommerce:clear');
</script>
<!-- End Google Analytics -->
<?php
}
?>
<span style="font-size: 0px">for analytics</span>
