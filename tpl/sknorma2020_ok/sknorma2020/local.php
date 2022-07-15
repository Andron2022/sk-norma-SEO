<?php
    /* local template modifications
    * functions and variables 
    * $u_vars = variables: [name] => value
    * $u_actions = to force url actions
    **/
    
   
    
    $u_actions = array(
        'go' => array(
            'file' => 'local_new.php', // file to include (local_new.php)
            'func' => 'u_redirect', // function name to load
        ),
    );


    function u_888redirect()
    {
    echo '7777777777777777777777';
    }
	
	/* function to change output data */
	function modify_page_data($site)
	{
		global $db, $tpl;
		$page = $site->page;
		if($page['page'] == 'glagol.html' && isset($page['categs'][0]['id'])){
			$page['list_pubs'] = list_pubs($page['categs'][0], $site, 'all', 100);			
		}
		
		$page['breadcrumbs_2'] = array();
		
		if(!empty($page['breadcrumbs'])){
			foreach($page['breadcrumbs'] as $k=>$v){
				if(is_array($v) && !empty($v)){
					foreach($v as $v1){
						$page['breadcrumbs_2'][] = $v1['alias'];
					}
				}else{
					$page['breadcrumbs_2'][] = $v['alias'];
				}
			}
		}
		
		/* detect if payd click by ?utm_source */
		$page['payd_click'] = !empty($_GET['utm_source']) ? trim($_GET['utm_source']) : '';
		if(!empty($page['payd_click'])){ $_SESSION['payd_click'] = $page['payd_click']; }
		if(!empty($_SESSION['payd_click'])){ $page['payd_click'] = $_SESSION['payd_click']; }
	
		if($site->user['id'] > 0){		
		/*
		echo '<pre>';
		print_r($page["blocks"]["sys"]);
		echo '</pre>';
		exit;
		*/
		}
		return $page;		
		
		echo '<pre>';
		//print_r($page['list_pubs']);
		print_r($page['connected_pubs']);
		echo '</pre>';
		exit;
		
	}

?>