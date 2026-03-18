<?php
$app = @$_REQUEST['app'];
if ($app == "android") {
	include '../config.php';
}
if (isset($_REQUEST['PTask'])) {
	function getleft($getval, $pro_amt, $utilObj)
	{
		$hasid = '';
		//$clientID = $_SESSION['Client_Id'];
		if ($_REQUEST['app'] == "android") {
			$clientID = $_REQUEST['ClientID'];
		} else {
			$clientID = $_SESSION['Client_Id'];
		}
		if ($_REQUEST['web'] == 'web' || $clientID == '') {
			$clientID = "mh";
		}
		//echo ">>".$getval;
		$datas = explode(",", $getval);
		foreach ($datas as $getid) {
			//	echo "##".$getid;
			$Gettid = $utilObj->getMultipleRow('users', "ClientID='" . $clientID . "' AND parentID='" . $getid . "' Order by position ASC ");
			if (count($Gettid) == 0) {
				return $_SESSION['data'] = $getid . "##left";
			} elseif (count($Gettid) == 1) {
				return $_SESSION['data'] = $getid . "##middle";
			} elseif (count($Gettid) == 2) {
				$getprosum = $utilObj->getSum("users", "ClientID='" . $clientID . "' AND parentID='" . $getid . "' AND flag='1' ", "pro_amt");
				if ($getprosum == '') {
					$getprosum = 0;
				}
				$prodsum = $getprosum + $pro_amt;
				$arrValue = array('pay_flag' => 1, 'pay_amt' => $prodsum);
				$strWhere = " userID='" . $getid . "' AND ClientID='" . $clientID . "' ";
				$Updaterec = $utilObj->updateRecord('users', $strWhere, $arrValue);
				return $_SESSION['data'] = $getid . "##right";
			} else {
				foreach ($Gettid as $myid) {
					$hasid .= $myid['userID'] . ",";
				}

			}
		}
		if ($hasid != '') {
			getleft(trim($hasid, ","), $pro_amt, $utilObj);
		}

	}

	switch ($_REQUEST['PTask']) {
		case "Add":
			if ($_REQUEST['sponsorUname'] == '') {
				// Fetch the first user as the sponsor if both are empty
				$user = $utilObj->getSingleRow("users", "1 LIMIT 1");
				$sponsorID = $user['userID']; // Make sure variable names match
			} else {
				// Use the provided sponsor username
				$sponsorID = $_REQUEST['sponsorUname'];
			}
			

			$password = encryptIt($_REQUEST['password']);
			
			$getrec = $utilObj->getSingleRow('users', "userID='" . $sponsorID . "' ");

			//$getcnt = $utilObj->getCount('users', "mobile='".$_REQUEST['mobile']."' ");
			//if($getcnt==''){$getcnt=0;}
			if ($app == "android") {
				$clientID = $_REQUEST['ClientID'];
			} else {
				$clientID = $_SESSION['Client_Id'];
			}

			if ($_REQUEST['web'] == 'web' || $clientID == '') {
				$clientID = "mh";
			}

			$getclient = $utilObj->getSingleRow("client", "ClientID='" . $clientID . "' ");




			if ($getrec != '') {
				$package = $utilObj->getSingleRow("package", "ClientID='" . $clientID . "' AND id='" . $_REQUEST['product'] . "' ");
				$getpro = $utilObj->getSingleRow('product', "ClientID='" . $clientID . "' AND id='" . $package['product'] . "' ");
				$pro_amt = $getpro['pro_amt'];
				$product_amt = $package['amount'];
				$product_perc = $getpro['perc'];
				$category = $getpro['category'];
				$refferal_amt = $getpro['refferal_amt'];
				$getclient = $utilObj->getSingleRow("client", "1");
				if ($_REQUEST['location'] == 'Satara City') {
					//$courier_chg = $getclient['courierchg_satara'];
					$courier_chg = $getpro['courier_satara'];
				} else {
					//$courier_chg = $getclient['courierchg_other'];
					$courier_chg = $getpro['courier_other'];
				}
				$getid = $_REQUEST['sponsorUname'];
				$str = getleft($getid, $pro_amt, $utilObj);
				//echo ">>>>>".$_SESSION['data'];
				$values = explode("##", $_SESSION['data']);
				$pos = $values['1'];
				$parentid = $values['0'];

				$parent = $utilObj->getSingleRow("users", "ClientID='" . $clientID . "' AND userID='" . $parentid . "' ");
				$prev_cnt = $utilObj->getCount("users", "ClientID='" . $clientID . "' AND parentID='" . $parentid . "' ");
				if ($prev_cnt == 0) {
					$right_val = ($parent['right_no'] * 3) - 2;
				} else if ($prev_cnt == 1) {
					$right_val = ($parent['right_no'] * 3) - 1;
				} else if ($prev_cnt == 2) {
					$right_val = $parent['right_no'] * 3;
				}
				$left_val = $parent['level'] + 1;

				$total_paid = $product_amt;

				$pay_method = $_REQUEST['pay_method'];

				$cashback_amount = $_REQUEST['cashback_amount'];
				$cashback_id = $_REQUEST['cashback_id'];
				$parent = $utilObj->getSingleRow("users", "ClientID='" . $clientID . "' AND userID='" . $sponsorID . "' ");


				$id = uniqid();
				$arrValue = array('id' => $id, 'ClientID' => $clientID, 'name' => $_REQUEST['name'], 'sponsorID' => $sponsorID, 'email' => $_REQUEST['email'], 'mobile' => $_REQUEST['mobile'], 'address' => $_REQUEST['address'], 'location' => $_REQUEST['location'], 'city' => $_REQUEST['city'], 'pincode' => $_REQUEST['pincode'], 'password' => $password, 'date' => date('Y-m-d', strtotime($_REQUEST['date'])), 'category' => $category, 'product' => $package['product'], 'product_color' => $_REQUEST['product_color'], 'product_size' => $_REQUEST['product_size'], 'product_amt' => $product_amt, 'refferal_amt' => $refferal_amt, 'product_perc' => $product_perc, 'pro_amt' => $pro_amt, 'parentID' => $parentid, 'position' => $pos, 'right_no' => $right_val, 'flag' => 1, 'courier_chg' => $courier_chg, 'total_paid' => $total_paid, 'pinID' => $_REQUEST['pinID'],'created' => date("Y-m-d H:i:s"), 'LastEdited' => date('Y-m-d H:i:s'), 'cashback_amount' => $cashback_amount, 'cashback_id' => $cashback_id);
				$insertedId = $utilObj->insertRecord('users', $arrValue);
				$user = $utilObj->getSingleRow("users", "id='" . $id . "' AND ClientID='" . $clientID . "' ");
				$jvWhere = " id='" . $id . "' AND ClientID='" . $clientID . "' ";
				$userID = $getclient['prefix'] . $user['uname'];

				$putjv = array('userID' => $userID);
				$updateuser = $utilObj->updateRecord('users', $jvWhere, $putjv);
				$currentSponsor = $sponsorID; // Start with the provided sponsor ID
$chainDepth = 0; 
$currentChainSponsor = $currentSponsor; // Keep track of the starting sponsor
$diamondDistribution = [4 => 5, 3 => 4, 2 => 3, 1 => 2]; // Diamond distribution mapping
$sponsor = $utilObj->getSingleRow("users", "userID='" . $sponsorID . "' AND ClientID='" . $clientID . "'");

$remainingDiamonds = $sponsor['distr_diamond_point']; // Total diamonds to distribute

if ($sponsor['diamond_point'] == 20) { // Only distribute if sponsor has exactly 20 diamonds
    while ($currentChainSponsor && $remainingDiamonds > 0) {
        $currentUser = $utilObj->getSingleRow("users", "userID='" . $currentChainSponsor . "' AND ClientID='" . $clientID . "'");
        if ($currentUser) {
            $currentDistrLevel = $currentUser['distr_level'];

            // Distribute diamonds only within the specified range
            if (isset($diamondDistribution[$currentDistrLevel])) {
                $diamondsToGive = $diamondDistribution[$currentDistrLevel];

                // Ensure not to exceed the remaining diamonds
                $diamondsToGive = min($diamondsToGive, $remainingDiamonds);

                // Update the diamond points for the current user
                $updatedDiamondPoints = !empty($currentUser['diamond_point']) ? $currentUser['diamond_point'] : 0;
                $updatedDiamondPoints += $diamondsToGive;

                // Calculate and update profit amount
                $profitAmount = !empty($currentUser['profit_amount']) ? $currentUser['profit_amount'] : 0;
                $profitAmount += $diamondsToGive * 500;

                // Update the record in the database
                $updateResult = $utilObj->updateRecord(
                    'users',
                    "userID='" . $currentUser['userID'] . "' AND ClientID='" . $clientID . "'",
                    array(
                        'diamond_point' => $updatedDiamondPoints,
                        'distr_diamond_point' => $updatedDiamondPoints,
                        'profit_amount' => $profitAmount
                    )
                );

                if ($updateResult) {
                    error_log("Successfully updated UserID: " . $currentUser['userID'] . " | New Diamond Points: " . $updatedDiamondPoints . " | New Profit Amount: " . $profitAmount);
                } else {
                    error_log("Failed to update UserID: " . $currentUser['userID']);
                }

                // Subtract distributed diamonds
                $remainingDiamonds -= $diamondsToGive;
            }

            // Move to the next sponsor in the chain
            $currentChainSponsor = $currentUser['sponsorID'];
        } else {
            error_log("No valid user found for UserID: " . $currentChainSponsor);
            break; // Exit if no valid user is found
        }
    }

    // Set distr_diamond_point to 0 after distribution
    $utilObj->updateRecord(
        'users',
        "userID='" . $sponsor['userID'] . "' AND ClientID='" . $clientID . "'",
        array('distr_diamond_point' => 0)
    );

    error_log("distr_diamond_point for UserID: " . $sponsor['userID'] . " set to 0 after distribution.");
}



    $parentUser = $utilObj->getSingleRow("users", "userID='" . $currentSponsor . "' AND ClientID='" . $clientID . "'");
    $parenttUser = $utilObj->getSingleRow("users", "userID='" . $parentUser['sponsorID'] . "' AND ClientID='" . $clientID . "'");
	$distrlevell = $parenttUser['distr_level'];
    if ($parentUser) {
        
            $distrLevel = $distrlevell+1; // Set distr_level based on the chain depth
            // Update the distr_level of the sponsor (not the current user)
            $utilObj->updateRecord(
                'users',
                "userID='" . $parentUser['userID'] . "' AND ClientID='" . $clientID . "'",
                array(
                    'distr_level' => $distrLevel
                )
            );
			
		}
			if ($parentUser['userID'] == $sponsorID) {
				// Ensure profit_amount is not NULL or empty, default to 0
				$profitAmount = !empty($parentUser['profit_amount']) ? $parentUser['profit_amount'] : 0;
				$newDiamondPoint = $profitAmount + $package['profit_amount'];
	
				// Calculate previous and current diamond points
				$previousDiamondPoints = floor($profitAmount / 500);
				$currentDiamondPoints = floor($newDiamondPoint / 500);
	
				// Add only 1 diamond point when crossing a 500 threshold
				$addedDiamondPoints = ($currentDiamondPoints > $previousDiamondPoints) ? 1 : 0;
	
				// Ensure diamond_point is not NULL, default to 0
				$DiamondPoint = !empty($parentUser['diamond_point']) ? $parentUser['diamond_point'] : 0;
				$DiamondPoint += $addedDiamondPoints+1;
	
				// Update the user's profit and diamond points
				$utilObj->updateRecord(
					'users',
					"userID='" . $parentUser['userID'] . "' AND ClientID='" . $clientID . "'",
					array(
						'profit_amount' => $newDiamondPoint,
						'diamond_point' => $DiamondPoint,
						'distr_diamond_point' => $DiamondPoint,
					)
				);
			}

		while ($currentSponsor) {
			$parentUser = $utilObj->getSingleRow("users", "userID='" . $currentSponsor . "' AND ClientID='" . $clientID . "'");
			if ($parentUser) {
				$currentLevel = $parentUser['level'];
				$distrLevel = $parentUser['distr_level'];
		
				// Update profit_amount and diamond_point **only for the root sponsor**
				
		
				// Increment level for the current user based on chain depth, only if current level is below 3
				if ($currentLevel < 3) {
					$newLevel = $chainDepth + 1; // Level is determined by the chain depth
					if ($newLevel > $currentLevel) {
						$utilObj->updateRecord(
							'users',
							"userID='" . $parentUser['userID'] . "' AND ClientID='" . $clientID . "'",
							array('level' => $newLevel)
						);
					}
				}
		
				// If the user's level reaches 3, mark their level_completed as 1
				if ($currentLevel == 2 && $chainDepth + 1 == 3) {
					$utilObj->updateRecord(
						'users',
						"userID='" . $parentUser['userID'] . "' AND ClientID='" . $clientID . "'",
						array('level_completed' => 1)
					);
				}
		
				// Stop adding levels for the root sponsor if level reaches 3
				if ($parentUser['userID'] === $sponsorID && $currentLevel >= 3) {
					break; // Root sponsor level is capped
				}
		
				// Move to the next sponsor in the chain
				$currentSponsor = $parentUser['sponsorID'];
				$chainDepth++; // Increment chain depth as we go up the chain
			} else {
				break; // Exit the loop if no valid parent user is found
			}
		}
		
		
$currentSponsor = $sponsorID; // Start with the provided sponsor ID
$chainDepth = 0; // Track the depth of the sponsorship chain (starts from 0)

while ($currentSponsor) {
    $parentUser = $utilObj->getSingleRow("users", "userID='" . $currentSponsor . "' AND ClientID='" . $clientID . "'");
    if ($parentUser) {
        $currentLevel = $parentUser['level'];


        // Increment level based on chain depth, only if current level is below 3
        if ($currentLevel < 3) {
            $newLevel = $chainDepth + 1; // Level is determined by the chain depth
            if ($newLevel > $currentLevel) {
                $utilObj->updateRecord(
                    'users',
                    "userID='" . $parentUser['userID'] . "' AND ClientID='" . $clientID . "'",
                    array('level' => $newLevel)
                );
            }
        }

        // Insert payout record for users at level 1, 2, or 3 based on their level
        if ($currentLevel == 1) {
            // First entry for level 1
            $product = $utilObj->getSingleRow("package", "ClientID='" . $clientID . "' AND product='" . $_REQUEST['product'] . "'");
            $parentID = uniqid();
            $arrValue = array(
                'id' => uniqid(),
                'ClientID' => $clientID,
                'parentID' => $parentID,
                'userID' => $parentUser['userID'],
                'date' => date('Y-m-d'),
                'level' => 1,
                'child_cnt' => 0, // Example value, you can replace it as needed
                'prodsum_amt' => 0, // Example value, replace it as needed
                'payout_amt' => 0, // Example value, replace it as needed
                'reward' => 0, // Example value, replace it as needed
                'total_income' => $product['level1'], // level1 income
                'Created' => date('Y-m-d H:i:s')
            );
            $utilObj->insertRecord('payout', $arrValue);
        } 

        if ($currentLevel == 2) {
            // Second entry for level 2
            $product = $utilObj->getSingleRow("package", "ClientID='" . $clientID . "' AND product='" . $_REQUEST['product'] . "'");
            $parentID = uniqid();
            $arrValue = array(
                'id' => uniqid(),
                'ClientID' => $clientID,
                'parentID' => $parentID,
                'userID' => $parentUser['userID'],
                'date' => date('Y-m-d'),
                'level' => 2,
                'child_cnt' => 0,
                'prodsum_amt' => 0,
                'payout_amt' => 0,
                'reward' => 0,
                'total_income' => $product['level2'], // level2 income
                'Created' => date('Y-m-d H:i:s')
            );
            $utilObj->insertRecord('payout', $arrValue);
        }

        if ($currentLevel == 3) {
            // Third entry for level 3
            $product = $utilObj->getSingleRow("package", "ClientID='" . $clientID . "' AND product='" . $_REQUEST['product'] . "'");
            $parentID = uniqid();
            $arrValue = array(
                'id' => uniqid(),
                'ClientID' => $clientID,
                'parentID' => $parentID,
                'userID' => $parentUser['userID'],
                'date' => date('Y-m-d'),
                'level' => 3,
                'child_cnt' => 0,
                'prodsum_amt' => 0,
                'payout_amt' => 0,
                'reward' => 0,
                'total_income' => $product['level3'], // level3 income
                'Created' => date('Y-m-d H:i:s')
            );
            $utilObj->insertRecord('payout', $arrValue);
        }

        // Move to the next sponsor in the chain
        $currentSponsor = $parentUser['sponsorID'];
        $chainDepth++; // Increment chain depth as we go up the chain
    } else {
        $currentSponsor = null; // No more sponsors in the chain
    }
}

				$messg = "Dear " . $_REQUEST['name'] . ", Welcome to OMS Shop & Earn. Thanks for registration with us. Your account will be activated after successful delivery of Product. For further details visit on http://omssind.com ";
				//	sms_send($_REQUEST['mobile'],$messg);

				$result = "success";
				$page = "COD";
				$msg = "Thanks for registration with us. Your account will be activated after successful delivery of Product. ";
				if ($app != "android") {
					if ($_REQUEST['web'] == 'web') {
						//echo "<script>window.top.location='login.php?suc=" . $msg . "'</script>";
						$name = $_REQUEST['name'];
						$email = $_REQUEST['email'];
						$amount = $product_amt;
						echo "<script>window.top.location = 'razorpay_payment.php?name={$name}&email={$email}&amount={$amount}';</script>";
					} else {
						echo "<script>window.top.location='registration.php?suc=" . $msg . "'</script>";
					}
				}

			} else {
				//if($getrec!=''){
				$msg = 'Sponsor Not Found! ';
				/*}else{
								$msg='Mobile Number already exist! ';
							}*/
				$result = "failed";
				if ($app != "android") {
					if ($_REQUEST['web'] == 'web') {
						echo "<script>window.top.location='checkout=" . $msg . "'</script>";
					} 
				}
			}
			if ($app == "android") {
				$response['result'] = $result;
				$response['message'] = $msg;
				$response['page'] = $page;
				$response['user'] = $user;
				echo json_encode($response);
			}

			
			break;


		case "aprove":
			$clientID = $_SESSION['Client_Id'];

			$pids = explode(",", $_REQUEST['val']);
			$arrValue = array('flag' => '1');
			foreach ($pids as $pid) {
				$strWhere = " id='" . $pid . "' ";
				$Updaterec = $utilObj->updateRecord('users_cod', $strWhere, $arrValue);

				$getuser = $utilObj->getSingleRow("users_cod", $strWhere);
				$flag = 1;
				//$getid = $getrec['userID'];
				$pro_amt = $getuser['pro_amt'];
				$getid = $getuser['sponsorID'];
				$str = getleft($getid, $pro_amt, $utilObj);
				//echo ">>>>>".$_SESSION['data'];
				$values = explode("##", $_SESSION['data']);
				$pos = $values['1'];
				$parentid = $values['0'];

				$parent = $utilObj->getSingleRow("users", "ClientID='" . $clientID . "' AND userID='" . $parentid . "' ");
				$prev_cnt = $utilObj->getCount("users", "ClientID='" . $clientID . "' AND parentID='" . $parentid . "' ");
				if ($prev_cnt == 0) {
					$right_val = ($parent['right_no'] * 3) - 2;
				} else if ($prev_cnt == 1) {
					$right_val = ($parent['right_no'] * 3) - 1;
				} else if ($prev_cnt == 2) {
					$right_val = $parent['right_no'] * 3;
				}
				$left_val = $parent['level'] + 1;


				$id = uniqid();
				$arrValue = array('id' => $id, 'ClientID' => $clientID, 'name' => $getuser['name'], 'sponsorID' => $getuser['sponsorID'], 'email' => $getuser['email'], 'mobile' => $getuser['mobile'], 'address' => $getuser['address'], 'location' => $getuser['location'], 'city' => $getuser['city'], 'pincode' => $getuser['pincode'], 'password' => $getuser['password'], 'category' => $getuser['category'], 'product' => $getuser['product'], 'product_color' => $getuser['product_color'], 'product_size' => $getuser['product_size'], 'product_amt' => $getuser['product_amt'], 'product_perc' => $getuser['product_perc'], 'pro_amt' => $getuser['pro_amt'], 'courier_chg' => $getuser['courier_chg'], 'total_paid' => $getuser['total_paid'], 'pinID' => $getuser['pinID'], 'parentID' => $parentid, 'position' => $pos, 'level' => $left_val, 'right_no' => $right_val, 'flag' => $flag, 'date' => date('Y-m-d'), 'pay_method' => 'COD', 'created' => date("Y-m-d H:i:s"), 'LastEdited' => date('Y-m-d H:i:s'), 'cashback_amount' => $getuser['cashback_amount'], 'cashback_id' => $getuser['cashback_id'], 'refferal_amt' => $getuser['refferal_amt']);
				$insertedId = $utilObj->insertRecord('users', $arrValue);

				$getclient = $utilObj->getSingleRow("client", "ClientID='" . $clientID . "' ");
				$user = $utilObj->getSingleRow("users", "id='" . $id . "' AND ClientID='" . $clientID . "' ");
				$jvWhere = " id='" . $id . "' AND ClientID='" . $clientID . "' ";
				$userID = $getclient['prefix'] . $user['uname'];

				$putjv = array('userID' => $userID);
				$updateuser = $utilObj->updateRecord('users', $jvWhere, $putjv);

				$getusr = $utilObj->getSingleRow("users", "ClientID='" . $clientID . "' AND id='" . $id . "' ");
				$password = decryptIt($getusr['password']);
				$messg = "Dear " . $getusr['name'] . ", Welcome to OMS Shop & Earn. Your account is activated now. Your Username is " . $userID . " and Password is " . $password . ". Visit http://omssind.com for login.";
				//	sms_send($getusr['mobile'],$messg);
			}

			if ($Updaterec)
				$Msg = 'Users aproved Sucessfully! ';

			if ($_REQUEST['type'] == '') {
				echo "<script>window.top.location='users_cod.php?suc=$Msg'</script>";
			} else {
				echo "<script>window.top.location='users_cod.php?type=" . $_REQUEST['type'] . "&suc=$Msg'</script>";
			}
			break;

		case "reject":
			$clientID = $_SESSION['Client_Id'];
			$pids = explode(",", $_REQUEST['val']);
			$arrValue = array('flag' => '2', 'reject_reason' => $_REQUEST['reason']);
			foreach ($pids as $pid) {
				$strWhere = " id='" . $pid . "' ";

				$Updaterec = $utilObj->updateRecord('users_cod', $strWhere, $arrValue);

			}

			if ($Updaterec)
				$Msg = 'Users rejected Sucessfully! ';

			if ($_REQUEST['type'] == '') {
				echo "<script>window.top.location='users_cod.php?suc=$Msg'</script>";
			} else {
				echo "<script>window.top.location='users_cod.php?type=" . $_REQUEST['type'] . "&suc=$Msg'</script>";
			}
			break;

		case "update_cod":
			$clientID = $_SESSION['Client_Id'];
			$sponsorID = $_REQUEST['sponsorUname'];
			$cashback_amount = $_REQUEST['cashback_amount'];
			$cashback_id = $_REQUEST['cashback_id'];
			$getrec = $utilObj->getSingleRow('users', "userID='" . $sponsorID . "' ");
			if ($getrec != '') {
				$password = encryptIt($_REQUEST['password']);
				$getpro = $utilObj->getSingleRow('product', "ClientID='" . $clientID . "' AND id='" . $_REQUEST['product'] . "' ");
				$pro_amt = $getpro['pro_amt'];
				$product_amt = $getpro['amount'];
				$product_perc = $getpro['perc'];
				$category = $getpro['category'];
				$getclient = $utilObj->getSingleRow("client", "1");
				/*if($_REQUEST['location']=='Satara City'){
								//$courier_chg = $getclient['courierchg_satara'];
								$courier_chg = $getpro['courier_satara'];
							}else{
								//$courier_chg = $getclient['courierchg_other'];
								$courier_chg = $getpro['courier_other'];
							}*/
				$courier_chg = $getpro['courier_satara'];

				$total_paid = $product_amt + $courier_chg;
				$arrValue = array('ClientID' => $clientID, 'name' => $_REQUEST['name'], 'sponsorID' => $sponsorID, 'email' => $_REQUEST['email'], 'mobile' => $_REQUEST['mobile'], 'address' => $_REQUEST['address'], 'location' => $_REQUEST['location'], 'city' => $_REQUEST['city'], 'pincode' => $_REQUEST['pincode'], 'password' => $password, 'category' => $category, 'product' => $_REQUEST['product'], 'product_color' => $_REQUEST['product_color'], 'product_size' => $_REQUEST['product_size'], 'product_amt' => $product_amt, 'courier_chg' => $courier_chg, 'product_perc' => $product_perc, 'pro_amt' => $pro_amt, 'total_paid' => $total_paid, 'LastEdited' => date('Y-m-d H:i:s'), 'cashback_amount' => $cashback_amount, 'cashback_id' => $cashback_id);
				$strWhere = " id='" . $_REQUEST['id'] . "' AND ClientID='" . $clientID . "' ";
				$Updaterec = $utilObj->updateRecord('users_cod', $strWhere, $arrValue);
				if ($Updaterec)
					$msg = 'Record has been Updated Sucessfully! ';

				echo "<script>window.top.location='users_cod.php?type=" . $_REQUEST['type'] . "&suc=$msg'</script>";
			} else {
				$msg = 'Please check Sponsor ID !!! ';
				echo "<script>window.top.location='registration_cod.php?id=" . $_REQUEST['id'] . "&type=" . $_REQUEST['type'] . "&suc=$msg'</script>";
			}
			break;

		/*case"update":
			  
				  $arrValue=array('ClientID'=>$client,'uname'=>$_REQUEST['uname'],'name'=>$_REQUEST['name'],'sponsorID'=>$_REQUEST['sponsorID'],'email'=>$_REQUEST['email'],'mobile'=>$_REQUEST['mobile'],'password'=>$password,'date'=>date('Y-m-d', strtotime($_REQUEST['date'])),'created'=>date("Y-m-d H:i:s"),'LastEdited'=>date('Y-m-d H:i:s'));
				  $strWhere=" id='".$_REQUEST['id']."' AND ClientID='".$_SESSION['Client_Id']."' ";
				  $Updaterec=$utilObj->updateRecord('users', $strWhere, $arrValue);
				  if($Updaterec)
					  $msg='Record has been Updated Sucessfully! ';
			  break;	
					  
			  case"delete":	
				  
				  $pids=explode(",",$_REQUEST['val']);
				  foreach($pids as $pid){	
				  $strWhere=" id='".$pid."' AND ClientID='".$_SESSION['Client_Id']."' ";
				  
				  $Deleterec=$utilObj->deleteRecord('vendor', $strWhere);	
				  
				  }
				  
				  
				  
			   
			  
					if($Deleterec)
				  $Msg='Record has been Deleted Sucessfully! ';
			  break;*/
	}
	//header("location:add_user.php?suc=$msg&savetype=".$_REQUEST['savetype']." ");
//echo "<script>window.top.location='add_vendor.php?suc=$Msg&savetype=".$_REQUEST['savetype']."'</script>";


}


?>