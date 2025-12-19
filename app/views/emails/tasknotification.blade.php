<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
		{{ HTML::style('css/bootstrap.min.css') }}
		{{ HTML::style('css/sb-admin-2.css') }}
		{{ HTML::style('font-awesome/css/font-awesome.min.css') }}
	</head>
	<body>
		<div class="col-lg-12">
			<div class="panel panel-default" align="center">
				<div class="panel-heading">
					<h1 class="page-header text-primary"></i></h1>
				</div>
                            <div class="panel-body" style="text-align:left;">
                                    <?php if($emailType == 'ASGN') {?>
                                    <table>
                                        <tr>
                                            <td>Task Number : <?php echo $taskNumber; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Assign By: <?php echo $assignedBy; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Description : <?php echo $description; ?> </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Transaction ID : <?php echo $order_id; ?></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <?php } ?>
                                <?php if($emailType == 'RSVD') {?>
                                    <table>
                                        <tr>
                                            <td>Task Number : <?php echo $taskNumber; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Assign To: <?php echo $assignTo; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Description : <?php echo $description; ?> </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Transaction ID : <?php echo $order_id; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Remarks : <?php echo $remarks; ?></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <?php } ?>
                                <?php if($emailType == 'CCLD') {?>
                                    <table>
                                        <tr>
                                            <td>Task Number : <?php echo $taskNumber; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Assign By: <?php echo $assignBy; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Description : <?php echo $description; ?> </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Transaction ID : <?php echo $order_id; ?></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Remarks : <?php echo $remarks; ?></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <?php } ?>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>	
		</div>
	</body>
</html>