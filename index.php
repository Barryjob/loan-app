<?php require_once 'connection/connection.php';?>
<?php require_once 'connection/function.php';?>

<?php
header('Content-Type: application/json; charset=UTF-8');
$action = $_POST['action']; ////GET PERFORM FUNCTION ON URL

switch ($action){


    case 'loan-request-api':
        $fullname=trim(strtoupper($_POST['fullname']));
        $loan_amount=trim($_POST['loan_amount']);
        $loan_duration=trim($_POST['loan_duration']);
        $loan_rate=(1.5);

        if (($fullname=="") || ($loan_amount=="") || ($loan_duration=="")){

            $response['response']=100;
            $response['success']=false;
            $response['message1']="ERROR!";
            $response['message2']="Fill all fields to continue.";

        } else{

            $sequence=$callclass->_get_sequence_count($conn, 'TRANS');
            $array = json_decode($sequence, true);
            $no= $array[0]['no'];
            $loan_id='TRANS'.date("Ymdhis").$no;


            mysqli_query($conn,"INSERT INTO `loan_request`
			(`loan_id`, `fullname`, `loan_amount`, `loan_duration`, `loan_rate`, `request_date`) VALUES
			('$loan_id','$fullname','$loan_amount','$loan_duration','$loan_rate',  NOW())")or die (mysqli_error($conn));
            

            $amount = $loan_amount;
            $month =  $loan_duration;
            $sub_payment = $amount / $month;
            for ($b = 1; $b <= $month; $b++) {
                $interest = ($loan_rate / 100) * $amount;
                $total = $sub_payment + $interest;
                $amount = $amount - $sub_payment;
                $total_repayment = $total_repayment + $total;
                $deduction =$amount + $sub_payment;

                mysqli_query($conn,"INSERT INTO `repayment_breakdown`
                (`months`,`loan_id`, `loan_balance`, `sub_payment`, `interest`, `loan_rate`, `total_repayment`) VALUES
                ('$b','$loan_id','$deduction','$sub_payment','$interest','$loan_rate', '$total_repayment')")or die (mysqli_error($conn));
            
            }


            $response['response']=101;
            $response['success']=true;
            $response['message1']="success!";
            $response['message2']="Loan request is successful.";
        }
    break;
 


    case 'fetch-loan-request-api':
        $loan_id=trim(strtoupper($_POST['loan_id']));

        if ($loan_id==''){//Start if 1
            $query = mysqli_query($conn, "SELECT * FROM loan_request");
            $check_query= mysqli_num_rows($query);
            if ($check_query>0) {// start if 2
                
                $response['response'] = 102;
                $response['success'] = true;
                $response['message1'] = "SUCCESS!";
                $response['message2'] = "User Record has been Successfully Fetched.";

                while($fetch_query = mysqli_fetch_all($query, MYSQLI_ASSOC)){
                    $response['data'] = $fetch_query;
                }
            }else{// else if 2
                $response['response'] = 103;
                $response['success'] = false;
                $response['message1'] = "Error!";
                $response['message2'] = "User record not found for the given user ID.";
            }// end if 2

        }else{// else if 1
            $query = mysqli_query($conn, "SELECT * FROM loan_request WHERE loan_id='$loan_id'");
            
            $response['response'] = 104;
            $response['success'] = true;

            while($fetch_query = mysqli_fetch_assoc($query)){
                $response['data'] = $fetch_query; 
            }
        }//end if 1
           
    break;



    case 'fetch-all-loan-requests-api':
        
        $loan_id=trim(strtoupper($_POST['loan_id']));
 
        $query = mysqli_query($conn, "SELECT loan_id, fullname, loan_amount, loan_duration, loan_rate, cummulative_repayment_amount, request_date FROM  loan_request WHERE loan_id='$loan_id'");
        $fetch_query = mysqli_fetch_array($query);

        if ($fetch_query > 0) {
            $loan_id = $fetch_query['loan_id'];
            $fullname = $fetch_query['fullname'];
            $loan_amount = $fetch_query['loan_amount'];
            $loan_duration = $fetch_query['loan_duration'];
            $request_date = $fetch_query['request_date'];

            $response['response'] = 007;
            $response['success'] = true;
            $response['loan_id'] = $loan_id;
            $response['fullname'] = $fullname;
            $response['loan_amount'] = $loan_amount;
            $response['loan_duration'] = $loan_duration;
            $response['request_date'] = $request_date;
            $response['message1'] = "SUCCESS!";
            $response['message2'] = "Transaction Record has been Successfully Fetched.";

            $query = mysqli_query($conn, "SELECT * FROM repayment_breakdown WHERE loan_id='$loan_id'");
            $fetch_data = array();
            $total_repayment = 0;
            while ($row = mysqli_fetch_assoc($query)) {
                $fetch_data[] = $row;
                $total_repayment += $row['total_repayment'];
            }
            $response['data'] = $fetch_data;
            $response['total_repayment'] = $total_repayment;

        } else {
    
            // $response['response'] = 008;
            $response['success'] = false;
            $response['message1'] = "Loan ID Error!";
            $response['message2'] = "No records found for the given loan ID.";
        }
     break;




   

}
echo json_encode($response); 
?>
