<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiInventory extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public static function UpdateQuantity($input=array())
    {
        $valid = 0;

        $post = array(
                'user'          => trim($input['user']), // Buyer Username
                'pass'          => trim($input['pass']), // Buyer Password
                'qrcode'        => $input['qrcode'],
                'priceopt'      => $input['priceopt'],
                'qty'           => $input['qty'],
                'type'          => $input['type'],
            );

        $user   = User::where('username', '=', $post['user'])->first();

        if (Hash::check($post['pass'], $user->password)) $valid = 1;

        if ($valid == 1)
        {
            foreach($post['priceopt'] as $k => $v)
            {
                $pre_stock = 0;

                $price = Price::find($post['priceopt'][$k]);

                if (count($price) > 0)
                {
                    $pre_stock = $price->stock;

                    switch ($post['type'][$k])
                    {
                        case 'plus':
                            $price->stock += $post['qty'][$k];
                            break;

                        case 'minus':
                            $price->stock -= $post['qty'][$k];
                            break;

                        case 'set':
                            $price->stock = $post['qty'][$k];
                            break;
                        
                        default:
                            # code...
                            break;
                    }

                    $price->save();

                    $row = array(
                        'qrcode'        => $post['qrcode'][$k],
                        'priceopt'      => $post['priceopt'][$k],
                        'type'          => $post['type'][$k],
                        'qty'           => $post['qty'][$k],
                        'stock'         => $price->stock,
                        'stock_unit'    => $price->stock_unit,
                        'pre_stock'     => $pre_stock,
                        'username'      => $post['user'],
                        'update_date'   => date('Y-m-d H:i:s'),
                        );

                    $insert = DB::table('jocom_inventory_history')->insert($row);

                    $insert_audit = General::audit_trail('ApiInventory.php', 'UpdateQuantity()', 'Stock Count', $post['user'], 'APP Inventory');

                    $data['item'][] = [
                        'qrcode'      => $post['qrcode'][$k],
                        'priceopt'    => $post['priceopt'][$k],
                        'stock'       => $price->stock,
                        'status'       => 'Updated',
                    ];
                }
                else
                {
                    $data['item'][] = [
                        'qrcode'      => $post['qrcode'][$k],
                        'priceopt'    => $post['priceopt'][$k],
                        'stock'       => 0,
                        'status'       => 'Error',
                    ];
                }
            }
        }
        else
            $data['status_msg'] = '#806';

        if ($data['item'] == NULL)
            $data['status_msg'] = '#806';
        

        return array('xml_data' => $data);
    }

    public static function getHistory($limit = 50, $offset = 0, $input=array())
    {
        $valid = 0;
        $data       = [
            'record'        => 0,
            'total_record'  => 0,
            'item'          => [],            
        ];

        $date = (isset($input['date']) AND $input['date'] != '') ? $input['date'] : date('Y-m-d');

        $post = array(
                'user'          => trim($input['user']), // Buyer Username
                'pass'          => trim($input['pass']), // Buyer Password
            );

        $user   = User::where('username', '=', $post['user'])->first();

        if (Hash::check($post['pass'], $user->password)) $valid = 1;

        if ($valid == 1)
        {
            $history = DB::table('jocom_inventory_history AS a')
                        ->select(array(
                            'a.id', 
                            'a.qrcode',
                            'a.priceopt',
                            'a.type',
                            'a.qty',
                            'a.stock',
                            'a.stock_unit',
                            'a.pre_stock',
                            'a.username',
                            'a.update_date',
                            'b.name',
                            'c.label',
                        ))
                        ->leftJoin('jocom_products AS b', 'b.qrcode', '=', 'a.qrcode')
                        ->leftJoin('jocom_product_price AS c', 'c.id', '=', 'a.priceopt')
                        ->where('a.username', $post['user'])
                        ->where('update_date', 'LIKE', '%'.$date.'%');

            $historyTotal = $history->count();
            $history = $history->skip($offset)->take($limit)->orderBy('id', 'desc')->get();

            $data['record']         = count($history);
            $data['total_record']   = $historyTotal;

            foreach($history as $row)
            {
                $data['item'][] = [
                        'qrcode'        => $row->qrcode,
                        'name'          => $row->name,
                        'label'         => $row->label,
                        'type'          => $row->type,
                        'qty'           => $row->qty,
                        'pre_stock'     => $row->pre_stock,
                        'stock'         => $row->stock,
                        'stock_unit'    => $row->stock_unit,
                        'update_date'   => $row->update_date,
                    ];
            }
        }
        else
            $data['status_msg'] = '#806';

        if ($data['item'] == NULL)
            $data['status_msg'] = '#806';
        

        return array('xml_data' => $data);
    }

   

}
?>