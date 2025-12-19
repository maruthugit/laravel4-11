
<style type="text/css">

    th {
        height: 30px;
    }
    .driver {
        height: 50px;
        margin: 20px;
    }
    .first{
      outline: thin solid transparent;
      height: 5px;
    }

    .center, th {
        text-align: center;
        vertical-align: middle;
    }
    .spac {
        outline: thin solid transparent; 
        height: 35px;
    }
</style>

<page backtop="7mm" backbottom="7mm" backleft="10mm" backright="10mm">
    <page_header></page_header>
    <table style="width: 100%">
        <col width="400">
        <col width="200">
        <tr>
            <td><h3>DRIVER TIME SHEET</h3></td>
            <!-- <td style="text-align: right">Date: {{$date}}</td> -->
            <td style="text-align: right"><h4>Date: {{$date}}</h4></td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; " border="1" cellpadding="15px">
        <col width="130">
        <col width="130">
        <col width="130">
        <col width="130">
        <col width="130">
        <thead>
            <tr>
                <th>Name</th>
                <th>DO Qty</th>
                <th>Roads</th>
                <th>Time Out</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            <tr class="spac"></tr>
            @foreach ($data as $driver)
            <tr>
                <td width="20%" class="center driver">{{$driver->driver_name}}</td>
                <td class="center driver" width="20%">{{$driver->do_qty}}</td>
                <td class="center driver" width="20%">{{$driver->roads}}</td>
                <td width="20%" class="center driver"></td>
                <td width="20%" class="center driver"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <page_footer></page_footer>
</page>
