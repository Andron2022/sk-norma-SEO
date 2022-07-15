<?php
	if (!class_exists('Site')) { return; }

    function start_lang_pack($lang_default, $site, $admin=false){
        global $path;		
		if(!empty($site->vars['lang_admin'])){
          $locat_file = $path.$site->vars['template_path'].'lang/'.strtolower($site->vars['lang_admin']).'.php';
          $admin_file = $path.$site->vars['template_path'].'lang/admin/'.strtolower($site->vars['lang_admin']).'.php';
          $lang = $site->vars['lang_admin'];
        }else{
			if(empty($site->vars['lang'])){ $site->vars['lang'] = 'en'; }
			$inc_lang_file = explode('_',$site->vars['lang']);
			$lang = $inc_lang_file[0];
			$locat_file = MODULE.'/lang/'.strtolower($lang).'.php';
			$admin_file = MODULE.'/lang/admin/'.strtolower($lang).'.php';
			//$lang = isset($site->vars['lang']) ? strtolower($site->vars['lang']) : $lang_default;
        }
        //$locat_file = $path.$site->vars['template_path'].'lang/'.strtolower($site->vars['lang']).'.php';
        $file_default = MODULE.'/lang/'.$lang_default.'.php';     
        $admin_default = MODULE.'/lang/admin/'.$lang_default.'.php';     
        //$lang = isset($site->vars['lang']) ? strtolower($site->vars['lang']) : $lang_default;
        $charset = isset($site->vars['site_charset']) ? strtolower($site->vars['site_charset']) : 'utf-8';
        $charset = str_replace('windows', 'win', $charset);
        $fn = ($charset == 'utf-8') ? $lang : $lang.'-'.$charset;
        $lang_current = MODULE.'/lang/'.$fn.'.php';
        $admin_current = MODULE.'/lang/admin/'.$fn.'.php';

        if(file_exists($locat_file)){
	       require($locat_file);
		   if(!empty($admin)){ require($admin_file); }
        }elseif(file_exists($lang_current)){
			require($lang_current);
			if(!empty($admin)){ 
				if(file_exists($locat_file)){
					require($admin_current); 
				}else{
					require($admin_default); 
				}
			}
        }else{
           require($file_default);
		   if(!empty($admin)){ require($admin_default); }
        }

		if($admin){ 
		
			if(!empty($site->vars['template_path'])){
				$src = PATH.'/'.ADMIN_FOLDER.'/inc/lang/'.$fn.'/template.php';
				if(file_exists($src)){
					require($src);
				}
			}
		
			return $lang; 
		}

		if(!empty($site->vars['template_path'])){
			$src = PATH.$site->vars['template_path'].'lang/'.$fn.'/template.php';
			if(file_exists($src)){
				require($src);
			}
		}
        $site->lang = $lang;
        return;
	}

    function GetMessage($var1='',$var2='',$var3=''){
        global $site,$tpl;		
        if(!empty($var1) && !empty($var2) && !empty($var3)){
            if(!isset($site->lang[$var1][$var2][$var3])){
                return $var1.'|'.$var2.'|'.$var3;
            }else{
                return $site->lang[$var1][$var2][$var3];
            }
        }elseif(!empty($var1) && !empty($var2)){
            if(!isset($site->lang[$var1][$var2])){
                return $var1.'|'.$var2;
            }else{
                return $site->lang[$var1][$var2];
            }
        }elseif(!empty($var1)){
            if(!isset($site->lang[$var1])){
                return $var1;
            }else{
                return $site->lang[$var1];
            }
        }else{
            return;
        }
    }
    
    /* function  from template */ 
    /* key1 | key2 | key3 */    
	if (class_exists('Template_Lite')) { 
		$tpl->register_function("lang", "GetMessageTpl");
	}
	
    function GetMessageTpl($ar)
    {
        global $site;
        $ar['key1'] = !empty($ar['key1']) ? $ar['key1'] : '';
        $ar['key2'] = !empty($ar['key2']) ? $ar['key2'] : '';
        $ar['key3'] = !empty($ar['key3']) ? $ar['key3'] : '';
        $ar['case'] = !empty($ar['case']) ? $ar['case'] : '';
		
        $endingArray = !empty($ar['word']) 
			&& !empty($site->lang[$ar['word']]
		) 
            ? $site->lang[$ar['word']] 
            : array($ar['key1'], $ar['key2'], $ar['key3']);
        
        if(!empty($ar['qty'])){
            return getNumEnding($ar['qty'], $endingArray);
        }
		
		$str = GetMessage($ar['key1'], $ar['key2'], $ar['key3']);
		if(!empty($ar['case']) && $ar['case'] == 'upper'){
			$str = mb_strtoupper($str, 'UTF-8');
		}elseif(!empty($ar['case']) && $ar['case'] == 'lower'){
			$str = mb_strtolower($str, 'UTF-8');
		}elseif(!empty($ar['case']) && $ar['case'] == 'first'){
			$fc = mb_strtoupper(mb_substr($str, 0, 1), 'UTF-8');
			$str = $fc.mb_substr($str, 1);
		}
		
        return $str;
    }
    
    
/**
 * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
 * @param  $number Integer Число на основе которого нужно сформировать окончание
 * @param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
 *         например array('яблоко', 'яблока', 'яблок')
 * @return String
 */
function getNumEnding($number, $endingArray)
{
    if(empty($endingArray[2])) $endingArray[2] = $endingArray[1];
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending=$endingArray[2];
        }
    }
    return $ending;
}    
 


?>