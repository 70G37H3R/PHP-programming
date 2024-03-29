<?php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}


$object->query = "
SELECT * FROM admin_table
WHERE admin_id = '".$_SESSION["admin_id"]."'
";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Thông Tin Cá Nhân</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-8"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Thông Tin Cá Nhân</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="admin_profile" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>Cập Nhật</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--<div class="row">
                                    <div class="col-md-6">!-->
                                        <div class="form-group">
                                            <label>Họ và tên</label>
                                            <input type="text" name="admin_name" id="admin_name" class="form-control" required data-parsley-pattern="/^[a-zA-Z0-9 \s]+$/" data-parsley-maxlength="175" data-parsley-trigger="keyup" />
                                        </div>
                                        <div class="form-group">
                                            <label>Tài khoản (Email)</label>
                                            <input type="text" name="admin_email_address" id="admin_email_address" class="form-control" required  data-parsley-type="email" data-parsley-trigger="keyup" />
                                        </div>
                                        <div class="form-group">
                                            <label>Mật khẩu</label>
                                            <input type="password" name="admin_password" id="admin_password" class="form-control" required data-parsley-maxlength="16" data-parsley-trigger="keyup" />
                                        </div>
                                   
                                    <!--</div>
                                </div>!-->
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    <?php
    foreach($result as $row)
    {
    ?>
    $('#admin_email_address').val("<?php echo $row['admin_email_address']; ?>");
    $('#admin_password').val("<?php echo $row['admin_password']; ?>");
    $('#admin_name').val("<?php echo $row['admin_name']; ?>");
  
  
    <?php
    }
    ?>

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"profile_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                    if(data.error != '')
                    {
                        $('#message').html(data.error);
                    }
                    else
                    {

                        $('#admin_email_address').val(data.admin_email_address);
                        $('#admin_password').val(data.admin_password);
                        $('#admin_name').val(data.admin_name);

                        $('#message').html(data.success);

    					setTimeout(function(){

    				        $('#message').html('');

    				    }, 5000);
                    }
				}
			})
		}
	});

});
</script>