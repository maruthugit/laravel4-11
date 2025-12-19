{{-- <pre>
{{ dd($finalList) }} --}}
{{-- </pre>  --}}
@if(count($finalList) > 0)
<table class="table" width="100%">
    <thead>
        <tr style="background: #26568a;color: #fff;">
            <th><!-- <input type="checkbox" name="check_all" id="check_all"> --></th>
            <th  style="width:15%;">Product Name</th>
            <th class="text-center" style="width:15%;">Product Sku</th>
            <th class="text-center" style="width:10%;">Required Set</th>
            <th class="text-center" style="width:10%;">Required Qty</th>
            <th class="text-center" style="width:15%;">Purchased Qty</th>
            <th class="text-center" style="width:15%;">Where Bought?</th>
            <th class="text-center" style="width:10%;">Remarks</th>
            <th class="text-center" style="width:10%">Unit Price</th>
        </tr>
    </thead>
    {{-- <pre>{{ dd($finalList) }}</pre> --}}
    <tbody>
    @foreach($finalList as $task)
        <tr class="task-item">
            <td>
                <input type="checkbox" data-url="{{ route('update.purchaseList', [$task['sort_trans_id'], $task['product_id']]) }}" {{ ($task['is_completed'] == 1) ? 'checked=true' : '' }} class="check-item">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" >
            </td>
            <td class="task-item-title {{ ($task['is_completed'] == 1) ? 'done' : '' }}">{{ $task['product_name'] }}</td>
            <td><span class="label label-default label-sm">{{ $task['product_sku'] }}</span></td>
            <td class="text-center">{{ $task['req_qty'] }}</td>
            <td></td>
            <td>
                <input type="text" class="form-control" name="purchased_quantity" id="purchased_qty" value="" placeholder="Purchased?">
            </td>
            <td>
                <input type="text" class="form-control" name="where_bought" id="where_bought" value="" placeholder="Where?">
            </td>
            <td><input type="text" class="form-control"  name="remarks" id="remarks" value="" placeholder="Remarks"></td>
            <td><span class="label label-primary label-sm">RM {{ $task['unit_price'] }}</span></td>
        </tr>
        @if(count($task['base_product']) > 0)
            @foreach($task['base_product'] as $b_list)
            <tr class="task-item">
                <td>
                    <input type="checkbox" class="check-item" data-url="{{ route('update.purchaseList', [$b_list['sort_trans_id'], $b_list['product_id']]) }}" {{ ($b_list['is_completed'] == 1) ? 'checked=true' : '' }}>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" >
                </td>
                <td class="task-item-title {{ ($b_list['is_completed'] == 1) ? 'done' : '' }}">{{ $b_list['product_name'] }}</td>
                <td><span class="label label-default label-sm">{{ $b_list['product_sku'] }}</span></td>
                <td class="text-center"></td>
                <td class="text-center">{{ $b_list['totalQuantity'] }}</td>
                <td>
                    <input type="text" class="form-control" name="purchased_quantity" id="purchased_qty" value="" placeholder="Purchased?">
                </td>
                <td>
                    <input type="text" class="form-control" name="where_bought" id="where_bought" value="" placeholder="Where?">
                </td>
                <td><input type="text" class="form-control"  name="remarks" id="remarks" value="" placeholder="Remarks"></td>
                <td><span class="label label-primary label-sm"></span></td>
            </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table>
@else
    <h4 class="text-center">No Purchase List(s) Available.</h4>
@endif
