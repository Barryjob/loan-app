<?php
class allClass{
function _get_sequence_count($conn, $item){
	$count=mysqli_fetch_array(mysqli_query($conn,"SELECT counter_value FROM setup_counters_tab WHERE counter_id = '$item' FOR UPDATE"));
	$num=$count[0]+1;
	mysqli_query($conn,"UPDATE `setup_counters_tab` SET `counter_value` = '$num' WHERE counter_id = '$item'")or die (mysqli_error($conn));
	if ($num<10){$no='00'.$num;}elseif($num>=10 && $num<100){$no='0'.$num;}else{$no=$num;}
	return '[{"num":"'.$num.'","no":"'.$no.'"}]';
}

function _get_loan_details($conn, $loan_id){
    $query=mysqli_query($conn, "SELECT * FROM loan_request WHERE loan_id='$loan_id'");
    $fetch=mysqli_fetch_array($query);
	$loan_id=$fetch['loan_id'];
	$fullname=$fetch['fullname'];
	$loan_amount=$fetch['loan_amount'];
	$loan_duration=$fetch['loan_duration'];
	$loan_rate=$fetch['loan_rate'];
	$request_date=$fetch['request_date'];
    return '[{"loan_id":"'.$loan_id.'","fullname":"'.$fullname.'","loan_amount":"'.$loan_amount.'","loan_duration":"'.$loan_duration.'","loan_rate":"'.$loan_rate.'","request_date":"'.$request_date.'"}]';
}


}$callclass=new allClass();
?>