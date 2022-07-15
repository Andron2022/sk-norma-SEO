<?php
	if (!class_exists('Site')) { return; }

    /***********************
    *
    * returns file if exists 
    *
    ***********************/ 
       
    //$s = new Getfile;

    class Getfile extends Site {

        function __construct()
        {
        }
                
        static public function get_file($site)
        {
			
            global $tpl, $db, $user;
            // ?l=621e7fb4e770b1d72208d888011cc28c&ext=docx&id=70
			// закрытые файлы должны иметь метку оплаченного заказа
			// getfile/?l=c81e728d9d4c2f636f067f89cc14862c
			// &ext=pdf
			// &id=1
			// &order=3d840f68f4e8b34ab00d6de7e5fd63fd
						
			
            $str = isset($_GET['l']) ? trim($_GET['l']) : "";
            $ext = isset($_GET['ext']) ? trim($_GET['ext']) : "";
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $order = isset($_GET['order']) ? trim($_GET['order']) : '';
            
            if(empty($str) || empty($ext) || $id == 0){
                return $site->error_page(404);
            }
            
            $row = $db->get_row("SELECT * FROM `uploaded_files` 
                WHERE MD5( id ) = '".$str."' 
                AND `ext` = '".$ext."' 
                AND record_id = '".$id."' ");
			//$db->debug(); exit;
            if(!empty($db->last_error)){ return $site->db_error(basename(__FILE__).": 35"); }
			
			
            if($db->num_rows == 1){
				
if(empty($site->user['id']) || empty($site->user['active']) || empty($site->user['admin'])){				
				$for_page = 'file';
				$for_id = $row->id;
		        $sess_current = session_id();
				$ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? 
					$_SERVER["REMOTE_ADDR"] : '';
					
				/* проверим можно ли загружать */
				/* allow_download = 0 - требуется метка оплаченного заказа */
				if(empty($row->allow_download)){
					if(empty($order)) {
						return $site->error_page(403);
					}
					$str2 = "SELECT * FROM ".$db->tables['orders']." WHERE 
							MD5(concat(id,created)) = '".$db->escape($order)."' 
							AND payd_status = '1' 
							";
					$row2 = $db->get_row($str2);
					if($row2 && $db->num_rows == 1){
						/* ок */
						
						$db->query("INSERT INTO ".$db->tables['counter']." 
							(`id_page`, `for_page`, `ip`, `time`, `hits`, `sess`) 
							VALUES ('".$row2->id."', 'order_file', '".$db->escape($ip_addr)."', '".date('Y-m-d H:i:s')."', 
							'1', '".$db->escape($sess_current)."') ");
							
							
						/* поставим статус заказу done */
						$sql = "SELECT id FROM ".$db->tables['order_status']." 
							WHERE `alias` = 'done'";
						$status_id = $db->get_var($sql);
						if(!empty($status_id)){
							$sql = "UPDATE ".$db->tables['orders']." 
								SET `status` = '".$status_id."' 
								WHERE id = '".$row2->id."' ";
							$db->query($sql);
						}
						
						/* отправим уведомление о скачанном файле */
						$msg = $site->GetMessage('words','file_downloaded').' '.$row->title.'.'.$row->ext;
						$ar = prepare_order_info($row2->id, $msg);
						$site->send_email_event('order_done', $ar);						

					}else{
						return $site->error_page(403);
					}
				}
				
				$db->query("INSERT INTO ".$db->tables['counter']." 
					(`id_page`, `for_page`, `ip`, `time`, `hits`, `sess`) 
					VALUES ('".$for_id."', '".$for_page."', 
					'".$db->escape($ip_addr)."', 
					'".date('Y-m-d H:i:s')."', '1', 
					'".$db->escape($sess_current)."') ");
					
					
				/* Добавим отметку, что файл скачан */
				$ip_addr = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''; 
				$mes = $site->GetMessage('words', 'file_downloaded').' '.$row->title;
				
				if(!empty($row2->id) && !empty($row2->email)){					
					$sql = "INSERT INTO ".$db->tables['comments']." 
						(record_type, record_id, userid, comment_text, 
						ddate, ip_address, unreg_email, active) 
						VALUES 
						('order', '".$order_id_dwnl."', '0', 
						'".$db->escape($mes)."', 
						'".date('Y-m-d H:i:s')."', '".$db->escape($ip_addr)."', 
						'".$email_id_dwnl."', '1') ";
					$db->query($sql);
				}
}
								
                /* OK let's download file */
            
                $title = $row->title.'.'.$row->ext;
                $title = str_replace('.'.$row->ext, '', $title);
                $filename = $title.".".$row->ext;
                $filename = str_replace('..','.', $filename);

      			$file = UPLOAD.'/files/'.$str.'.'.$ext;			

                if (file_exists($file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename='.basename($filename));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . $row->size);
                    @ob_clean();
                    flush();
                    readfile($file);
                    exit;
                }else{
                    return $site->error_message('File <b>'.$filename.'</b> not found');
                }
            
            }else{
                return $site->error_page(404);
            }
        }
        
    }

?>