<?php

//appointment_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Admin')
		{
			$order_column = array('appointment_table.appointment_number', 'patient_table.patient_first_name', 'doctor_table.doctor_name', 'doctor_schedule_table.doctor_schedule_date', 'appointment_table.appointment_time', 'doctor_schedule_table.doctor_schedule_day', 'appointment_table.status');
			$main_query = "
			SELECT * FROM appointment_table  
			INNER JOIN doctor_table 
			ON doctor_table.doctor_id = appointment_table.doctor_id 
			INNER JOIN doctor_schedule_table 
			ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id 
			INNER JOIN patient_table 
			ON patient_table.patient_id = appointment_table.patient_id 
			";

			$search_query = '';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'WHERE doctor_schedule_table.doctor_schedule_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" AND (';
			}
			else
			{
				$search_query .= 'WHERE ';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'appointment_table.appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR patient_table.patient_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR patient_table.patient_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR doctor_table.doctor_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR doctor_schedule_table.doctor_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.appointment_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR doctor_schedule_table.doctor_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.status LIKE "%'.$_POST["search"]["value"].'%" ';
			}
			if($_POST["is_date_search"] == "yes")
			{
				$search_query .= ') ';
			}
			else
			{
				$search_query .= '';
			}
		}
		else
		{
			$order_column = array('appointment_table.appointment_number', 'patient_table.patient_first_name', 'doctor_schedule_table.doctor_schedule_date', 'appointment_table.appointment_time', 'doctor_schedule_table.doctor_schedule_day', 'appointment_table.status');

			$main_query = "
			SELECT * FROM appointment_table 
			INNER JOIN doctor_schedule_table 
			ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id 
			INNER JOIN patient_table 
			ON patient_table.patient_id = appointment_table.patient_id 
			";

			$search_query = '
			WHERE appointment_table.doctor_id = "'.$_SESSION["admin_id"].'" 
			';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'AND doctor_schedule_table.doctor_schedule_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" ';
			}
			else
			{
				$search_query .= '';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'AND (appointment_table.appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR patient_table.patient_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR patient_table.patient_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR doctor_schedule_table.doctor_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.appointment_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR doctor_schedule_table.doctor_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR appointment_table.status LIKE "%'.$_POST["search"]["value"].'%") ';
			}
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY appointment_table.appointment_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["appointment_number"];

			$sub_array[] = $row["patient_first_name"] . ' ' . $row["patient_last_name"];

			if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = $row["doctor_name"];
			}
			$sub_array[] = date('d-m-Y', strtotime($row["doctor_schedule_date"]));

			$sub_array[] = $row["appointment_time"];

			$dayrs= 'Chủ Nhật';
			if ($row["doctor_schedule_day"] == 'Monday')
				$dayrs= 'Thứ Hai';
			else if ($row["doctor_schedule_day"] == 'Tuesday')
				$dayrs= 'Thứ Ba';
			else if ($row["doctor_schedule_day"] == 'Wednesday')
				$dayrs= 'Thứ Tư';
			else if ($row["doctor_schedule_day"] == 'Thursday')
				$dayrs= 'Thứ Năm';
			else if ($row["doctor_schedule_day"] == 'Friday')
				$dayrs= 'Thứ Sáu';
			else if ($row["doctor_schedule_day"] == 'Saturday')
				$dayrs= 'Thứ Bảy';
			

			$sub_array[] = $dayrs;

			$status = '';

			if($row["status"] == 'Booked')
			{
				$status = '<span class="badge badge-warning">Đã Hẹn</span>';
			}

			if($row["status"] == 'In Process')
			{
				$status = '<span class="badge badge-primary">Đang Xử Lý</span>';
			}

			if($row["status"] == 'Completed')
			{
				$status = '<span class="badge badge-success">Thành công</span>';
			}

			if($row["status"] == 'Cancel')
			{
				$status = '<span class="badge badge-danger">Hủy</span>';
			}

			$sub_array[] = $status;

			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["appointment_id"].'"><i class="fas fa-eye"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM appointment_table 
		WHERE appointment_id = '".$_POST["appointment_id"]."'
		";

		$appointment_data = $object->get_result();

		foreach($appointment_data as $appointment_row)
		{

			$object->query = "
			SELECT * FROM patient_table 
			WHERE patient_id = '".$appointment_row["patient_id"]."'
			";

			$patient_data = $object->get_result();

			$object->query = "
			SELECT * FROM doctor_schedule_table 
			INNER JOIN doctor_table 
			ON doctor_table.doctor_id = doctor_schedule_table.doctor_id 
			WHERE doctor_schedule_table.doctor_schedule_id = '".$appointment_row["doctor_schedule_id"]."'
			";

			$doctor_schedule_data = $object->get_result();

			$html = '
			<h4 class="text-center">Thông Tin Bệnh Nhân</h4>
			<table class="table">
			';

			foreach($patient_data as $patient_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-left">Họ và tên</th>
					<td>'.$patient_row["patient_first_name"].' '.$patient_row["patient_last_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-left">SĐT Liên Lạc</th>
					<td>'.$patient_row["patient_phone_no"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-left">Địa Chỉ Liên Lạc</th>
					<td>'.$patient_row["patient_address"].'</td>
				</tr>
				';
			}

			$html .= '
			</table>
			<hr />
			<h4 class="text-center">Thông Tin Cuộc Hẹn</h4>
			<table class="table">
				<tr>
					<th width="40%" class="text-left">Số thứ tự cuộc hẹn</th>
					<td>'.$appointment_row["appointment_number"].'</td>
				</tr>
			';
			foreach($doctor_schedule_data as $doctor_schedule_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-left">Tên Bác Sĩ</th>
					<td>'.$doctor_schedule_row["doctor_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-left">Ngày Hẹn</th>
					<td>'.$doctor_schedule_row["doctor_schedule_date"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-left">Thứ Hẹn</th>
					<td>'.$doctor_schedule_row["doctor_schedule_day"].'</td>
				</tr>
				
				';
			}

			$html .= '
				<tr>
					<th width="40%" class="text-left">Giờ Hẹn</th>
					<td>'.$appointment_row["appointment_time"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-left">Lí Do</th>
					<td>'.$appointment_row["reason_for_appointment"].'</td>
				</tr>
			';

			if($appointment_row["status"] != 'Cancel')
			{
				if($_SESSION['type'] == 'Admin')
				{
					if($appointment_row['patient_come_into_hospital'] == 'Yes')
					{
						if($appointment_row["status"] == 'Completed')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-left">Xác nhận bệnh nhân đến khám</th>
									<td>Có</td>
								</tr>
								<tr>
									<th width="40%" class="text-left">Lời dặn của bác sĩ</th>
									<td>'.$appointment_row["doctor_comment"].'</td>
								</tr>
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-left">Xác nhận bệnh nhân đến khám</th>
									<td>
										<select name="patient_come_into_hospital" id="patient_come_into_hospital" class="form-control" required>
											<option value="">Select</option>
											<option value="Yes">Có</option>
											<option value="No">Không</option>
										</select>
									</td>
								</tr
							';
						}
					}
					else
					{
						$html .= '
							<tr>
								<th width="40%" class="text-left">Xác nhận bệnh nhân đến khám</th>
								<td>
									<select name="patient_come_into_hospital" id="patient_come_into_hospital" class="form-control" required>
										<option value="">Select</option>
										<option value="Yes">Có</option>
										<option value="No">Không</option>
									</select>
								</td>
							</tr
						';
					}
				}

				if($_SESSION['type'] == 'Doctor')
				{
					if($appointment_row["patient_come_into_hospital"] == 'Yes')
					{
						if($appointment_row["status"] == 'Completed')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-left">Lời dặn của bác sĩ</th>
									<td>
										<textarea name="doctor_comment" id="doctor_comment" class="form-control" rows="8" required>'.$appointment_row["doctor_comment"].'</textarea>
									</td>
								</tr
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-left">Lời dặn của bác sĩ</th>
									<td>
										<textarea name="doctor_comment" id="doctor_comment" class="form-control" rows="8" required></textarea>
									</td>
								</tr
							';
						}
					}
				}
			
			}

			$html .= '
			</table>
			';
		}

		echo $html;
	}

	if($_POST['action'] == 'change_appointment_status')
	{
		if($_SESSION['type'] == 'Admin')
		{	
			if (isset($_POST['patient_come_into_hospital'])) {
				if($_POST['patient_come_into_hospital'] == 'Yes'){

				
				$data = array(
					':status'							=>	'In Process',
					':patient_come_into_hospital'		=>	'Yes',
					':appointment_id'					=>	$_POST['hidden_appointment_id']
				);

				$object->query = "
				UPDATE appointment_table 
				SET status = :status, 
				patient_come_into_hospital = :patient_come_into_hospital 
				WHERE appointment_id = :appointment_id
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Thay đổi trạng thái Đang Xử Lý</div>';
				}
				else {
					$data = array(
						':status'							=>	'Cancel',
						':patient_come_into_hospital'		=>	'No',
						':appointment_id'					=>	$_POST['hidden_appointment_id']
					);
		
					$object->query = "
					UPDATE appointment_table 
					SET status = :status, 
					patient_come_into_hospital = :patient_come_into_hospital 
					WHERE appointment_id = :appointment_id
					";
		
					$object->execute($data);
		
					echo '<div class="alert alert-success">Thay đổi trạng thái Hủy</div>';
				}
			}
		}

		if($_SESSION['type'] == 'Doctor')
		{
			if(isset($_POST['doctor_comment']))
			{
				$data = array(
					':status'							=>	'Completed',
					':doctor_comment'					=>	$_POST['doctor_comment'],
					':appointment_id'					=>	$_POST['hidden_appointment_id']
				);

				$object->query = "
				UPDATE appointment_table 
				SET status = :status, 
				doctor_comment = :doctor_comment 
				WHERE appointment_id = :appointment_id
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Thay đổi trạng thái Thành Công</div>';
			}
		}
	}
	

}

?>