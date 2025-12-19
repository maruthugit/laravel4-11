@if(count($finalList) > 0)
<form name="frm-fresh-inventory" action="/warehouse/sorted/purchase-todos-update" id="frm-fresh-inventory" method="POST">
    <table id="fresh-inventory-items-table" class="table table-striped table-hover" cellspacing="0" width="90%">
        <thead>
            <tr>
                <th><!-- <input type="checkbox" name="check_all" id="check_all"> --></th>
                <th width="15%">Name</th>
                <th class="text-center">Sku</th>
                <th class="text-center">Required Set</th>
                <th class="text-center">Required Qty</th>
                <th class="text-center">Purchased Qty/Set</th>
                <th class="text-center">Where purchased?</th>
                <th class="text-center">Remarks</th>
                <th class="text-center">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finalList as $i => $task)
                <tr class="task-item">
                    <td>
                        <div class="pretty p-default">
                            <input type="checkbox" name="data[{{$j}}][check_completed]" id="is_completed" {{ ($task['is_completed'] == 1 || !empty($task['where_purchased'])) ? 'checked' : '' }} value="1">
                            <div class="state p-success">
                                <label></label>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" >
                    </td>
                    <td>
                        <span class="sml-txt">{{ $task['product_name'] }}</span>
                        <input type="hidden" name="data[{{$i}}][sorted_tansaction_id]" value="{{$task['sort_trans_id']}}">
                        <input type="hidden" name="data[{{$i}}][product_id]" value="{{$task['product_id']}}">
                    </td>
                    <td>
                        <span class="label label-default">{{ $task['product_sku'] }}</span>
                        <input class="form-control" readonly type="hidden" name="data[{{$i}}][sku]" value="{{ $task['product_sku'] }}" size="3">
                    </td>
                    <td>
                        <input class="form-control" readonly type="text" name="data[{{$i}}][req_set]" value="{{ $task['req_qty'] }}" size="3">
                    </td>
                    <td>
                        <input readonly class="form-control" type="text" name="data[{{$i}}][req_qty]" value="" size="3">
                    </td>
                    <td>
                        <input class="form-control" type="text" name="data[{{$i}}][purchased_quantity_set]" value="{{ ($task['purchased_quantity_set'] != null) ? $task['purchased_quantity_set'] : $task['req_qty'] }}" size="3">
                    </td>
                    <td>
                        <input class="form-control" type="text" name="data[{{$i}}][where_purchased]" value="{{ $task['where_purchased'] }}" size="3">
                    </td>
                    <td><textarea class="form-control" name="data[{{$i}}][inventory_remarks]" rows="2" cols="10">{{ $task['inventory_remarks'] }}</textarea></td>
                    <td class="text-center">
                        <span class="label label-primary">{{ ($task['unit_price'] == "-") ? "" : "RM ".$task['unit_price'] }}</span>
                        <input type="hidden" name="data[{{$i}}][unit_price]" value="{{ $task['unit_price'] }}">
                    </td>
                </tr>
                @if(count($task['base_product']) > 0)
                    @foreach($task['base_product'] as $j => $b_list)
                    <tr class="task-item">
                        <td>
                            <div class="pretty p-default">
                                <input type="checkbox" name="baseItemData[{{$j}}][check_completed]" id="is_completed" {{ ($b_list['is_completed'] == 1 || !empty($b_list['where_purchased'])) ? 'checked' : '' }} value="1">
                                <div class="state p-success">
                                    <label></label>
                                </div>
                            </div>
                            {{-- <input type="checkbox" name="baseItemData[{{$j}}][check_completed]" id="is_completed" {{ ($b_list['is_completed'] == 1 || !empty($b_list['where_purchased'])) ? 'checked' : '' }} value="1"> --}}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" >
                        </td>
                        <td class="task-item-title">
                            {{ $b_list['product_name'] }}
                            <input type="hidden" name="baseItemData[{{$j}}][sorted_tansaction_id]" value="{{$b_list['sort_trans_id']}}">
                            <input type="hidden" name="baseItemData[{{$j}}][product_id]" value="{{$b_list['product_id']}}">
                        </td>
                        <td>
                            <span class="label label-default label-sm">{{ $b_list['product_sku'] }}</span>
                            <input class="form-control" readonly type="hidden" name="baseItemData[{{$j}}][sku]" value="{{ $b_list['product_sku'] }}" size="3">
                        </td>
                        <td class="text-center">
                            <input readonly class="form-control" type="text" name="baseItemData[{{$j}}][req_set]" value="" size="3">
                        </td>
                        <td class="text-center">
                            <input readonly class="form-control" readonly type="text" name="baseItemData[{{$j}}][req_qty]" value="{{ $b_list['totalQuantity'] }}" size="3">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="baseItemData[{{$j}}][purchased_quantity_set]" value="{{ ($b_list['purchased_quantity_set'] != null) ? $b_list['purchased_quantity_set'] : $b_list['totalQuantity'] }}" size="3">
                        </td>
                        <td>
                            <input class="form-control" type="text" name="baseItemData[{{$j}}][where_purchased]" value="{{ $b_list['where_purchased'] }}" size="3">
                        </td>
                        <td><textarea class="form-control" name="baseItemData[{{$j}}][inventory_remarks]" rows="2" cols="10">{{ $b_list['inventory_remarks'] }}</textarea></td>
                        <td class="text-center">
                            <span class="label label-primary">{{ ($b_list['unit_price'] == "-") ? "" : "RM ".$b_list['unit_price'] }}</span>
                            <input type="hidden" name="baseItemData[{{$j}}][unit_price]" value="{{ $b_list['unit_price'] }}">
                        </td>
                    </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
    <hr>
    <p class="text-center">
        <button class="btn btn-md btn-primary">Submit</button>
        <button type="button" class="btn btn-danger btn-md" data-dismiss="modal">Close</button>
    </p>

</form>
@else
    <h4 class="text-center">No Purchase List(s) Available.</h4>
@endif

<script>
     $("#check_all").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
</script>