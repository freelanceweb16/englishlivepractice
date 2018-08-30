<?php

	include_once('config.php');

		
		$requetef2 = "SELECT * FROM mod362_woocommerce_order_items";
		$resultatf2 = $pdo->query($requetef2) or die(print_r($pdo->errorInfo()));
		
		while($rowf2 = $resultatf2->fetch()) {
			
			$requetef3 = "SELECT * FROM mod362_postmeta WHERE post_id = '".$rowf2['order_id']."' AND meta_key = '_stripe_charge_captured'";
			$resultatf3 = $pdo->query($requetef3) or die(print_r($pdo->errorInfo()));
			$rowcountf3 = $resultatf3->rowCount();
			
			$countifalreadycompleted = "SELECT * FROM mod362_posts WHERE ID = '".$rowf2['order_id']."' AND post_status != 'wc-completed'";
			$resultcomplexted = $pdo->query($countifalreadycompleted) or die(print_r($pdo->errorInfo()));
			$rowccompleted = $resultcomplexted->rowCount();
			
			if($rowccompleted != 0){
			
			
				if($rowcountf3 == 1){
				while($rowf3 = $resultatf3->fetch()) {
		
					if($rowf3['meta_value'] == 'yes'){
						
						echo 'ORDER '.$rowf2['order_id'].' COMPLETED<br />';
						
						/* UPDATE USER PROFIL LAST ORDERID */
						$requetef3a = "SELECT * FROM mod362_postmeta WHERE post_id = '".$rowf2['order_id']."' AND meta_key = '_customer_user'";
						// $userid = $rowf2['order_id'];
						$resultatf3a = $pdo->query($requetef3a) or die(print_r($pdo->errorInfo()));
						while($rowf3a = $resultatf3a->fetch()) {
							
							$userid = $rowf3a['meta_value'];
							
							$requetef3b = "SELECT * FROM mod362_usermeta WHERE user_id = '".$userid."' AND meta_key = 'lastorderid'";
							$resultatf3b = $pdo->query($requetef3b) or die(print_r($pdo->errorInfo()));
							$countf3b = $resultatf3b->rowCount();
							if($countf3b == 0){
								$requetef3c = "INSERT INTO mod362_usermeta (user_id, meta_key, meta_value) VALUES ('".$userid."', 'lastorderid', '".$rowf2['order_id']."')";
								$pdo->query($requetef3c) or die(print_r($pdo->errorInfo()));
							}else{
								$requetef3c = "UPDATE mod362_usermeta SET meta_value = '".$rowf2['order_id']."' WHERE user_id = '".$userid."' AND meta_key = 'lastorderid'";
								$pdo->query($requetef3c) or die(print_r($pdo->errorInfo()));
							}
							
							/* ORDER UPDATE COUNT */
							$option = explode(' ', $rowf2['order_item_name']);
							
							if($option[0] == 'Forfait'){
								
								$requetef5c = "UPDATE mod362_usermeta SET meta_value = '".$option[1]."' WHERE user_id = '".$userid."' AND meta_key = 'credit_abonnement'";
								$pdo->query($requetef5c) or die(print_r($pdo->errorInfo()));
								
								$requetef5d = "UPDATE mod362_usermeta SET meta_value = '".$option[0]."' WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'abonnement'";
								$pdo->query($requetef5d) or die(print_r($pdo->errorInfo()));
								
								$requetef5e = "UPDATE mod362_usermeta SET meta_value = '' WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'fin_de_labonnement'";
								$pdo->query($requetef5e) or die(print_r($pdo->errorInfo()));
								
							}else{
								echo 'ABONNEMENT';
								$requetef5c = "UPDATE mod362_usermeta SET meta_value = '' WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'credit_abonnement'";
								$pdo->query($requetef5c) or die(print_r($pdo->errorInfo()));
								
								
								$requetef5d = "UPDATE mod362_usermeta SET meta_value = '".$option[0]."' WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'abonnement'";
								$pdo->query($requetef5d) or die(print_r($pdo->errorInfo()));
								
								
								$datenow = date('d-m-Y');
								$monthplus = '+'.$option[1].' months';
								$dateplusmonth = date('d-m-Y', strtotime($datenow.''.$monthplus));
									$c5count = $pdo->query("SELECT * FROM mod362_usermeta WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'fin_de_labonnement'") or die(print_r($pdo->errorInfo()));
									$c5c = $c5count->rowCount();
									if($c5c == 0){
										$requetef5e = "INSERT INTO mod362_usermeta (meta_value, user_id, meta_key) VALUES('".$dateplusmonth."', '".$rowf3a['meta_value']."', 'fin_de_labonnement')";
										$pdo->query($requetef5e) or die(print_r($pdo->errorInfo()));
									}else{
										$requetef5e = "UPDATE mod362_usermeta SET meta_value = '".$dateplusmonth."' WHERE user_id = '".$rowf3a['meta_value']."' AND meta_key = 'fin_de_labonnement'";
										$pdo->query($requetef5e) or die(print_r($pdo->errorInfo()));
									}
							}
							
							/* UPDATE ORDER STATE */
							$queryfg = "UPDATE mod362_posts SET post_status = 'wc-completed' WHERE ID = '".$rowf2['order_id']."'";
							$pdo->query($queryfg) or die(print_r($pdo->errorInfo()));
							
						}
						
						
						
					}else{
						echo 'ORDER '.$rowf2['order_id'].' NOT COMPLETED<br />';
					}
					
				}
				}else{}
			
			}else{}
			
		}
?>