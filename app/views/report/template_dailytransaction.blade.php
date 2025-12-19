<html>
    <!-- Headings -->
    <table>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="10"><h1>Daily Transaction From {{$date_from}} To {{$date_to}}</h1></td>
    </tr>
    <tr>
         <td>&nbsp;</td>
    </tr>
    </table>

    
    <table>
        <tr>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">Platform</h4></td>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">SKU</h4></td>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">Product Name</h4></td>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">Price Label</h4></td>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">Transaction Count</h4></td>
            <td style="border: 2px solid  #000000;background-color: #9a9a9a; vertical-align: middle;"><h4 style="color: #fff">Total Required</h4></td>
        </tr>
    @foreach ($company_list as $seller => $details)
        <tr>
            <td colspan="6" style="background-color: #ddd; border: 2px solid  #000000;; vertical-align: middle; text-align: center"><h3>{{$seller}}</h3></td>
        </tr>
        @foreach ($details as $detail)
        <tr>
            <td style="border: 2px solid  #000000;">{{$detail['platform']}}</td>
            <td style="border: 2px solid  #000000;">{{$detail['sku']}}</td>
            <td style="border: 2px solid  #000000;">{{$detail['name']}}</td>
            <td style="border: 2px solid  #000000;">{{$detail['label']}}</td>
            <td style="border: 2px solid  #000000;">{{$detail['transaction_count']}}</td>
            <td style="border: 2px solid  #000000;">{{$detail['total_required']}}</td>
        </tr>
        @endforeach
    @endforeach
    </table>

</html>