<?php 

class protect
{
	var $errors;
	var $license_key;
	var $secret_key;
	var $api_server;
	var $remote_port;
	var $remote_timeout = 5;
	var $local_key_storage;
	var $read_query;
	var $update_query;
	var $local_key_path;
	var $local_key_name;
	var $local_key_transport_order;
	var $local_key_grace_period;
	var $local_key_last;
	var $validate_download_access;
	var $release_date;
	var $key_data;
	var $status_messages;
	var $valid_for_product_tiers;
	var $site_id = 0;
	var $payd_till = null;
	var $initiate = null;

	/*
	value -> if_enum
	local_key -> sys_licence
	license_key -> value
	key => name
	*/
	function __construct()
    {		
	}
        
	function protect()
	{
		$this->errors=false;
		$this->remote_port=80;
		$this->remote_timeout=10;
		$this->valid_local_key_types=array('spbas');
		$this->local_key_type='spbas';
		$this->local_key_storage='database';  
		$this->local_key_grace_period=0;
		$this->local_key_last=0;
		$this->local_key_path='./';
		$this->local_key_name='license.txt';
		$this->local_key_transport_order='scf';
		$this->validate_download_access=false;
		$this->release_date=false;
		$this->valid_for_product_tiers=false;

		$this->key_data=array(
						'custom_fields' => array(), 
						'download_access_expires' => 0, 
						'license_expires' => 0, 
						'local_key_expires' => 0, 
						'status' => 'Invalid', 
						);

		$this->status_messages=array(
						'active' => 'This license is active.', 
						'suspended' => 'Error: This license has been suspended.', 
						'expired' => 'Error: This license has expired.', 
						'pending' => 'Error: This license is pending review.', 
						'download_access_expired' => 'Error: This version of the software was released '.
													 'after your download access expired. Please '.
													 'downgrade or contact support for more information.', 
						'missing_license_key' => 'Ошибка. Не указан лицензионный ключ.',
						'unknown_local_key_type' => 'Error: An unknown type of local key validation was requested.',
						'could_not_obtain_local_key' => 'Ошибка: Невозможно получить новый локальный ключ. Возможно, отсутствует соединение с сервером.', 
						'maximum_grace_period_expired' => 'Error: The maximum local license key grace period has expired.',
						'local_key_tampering' => 'Ошибка local_key_tampering: Локальный ключ был изменен или является недопустимым.',
						'local_key_invalid_for_location' => 'Ошибка: Локальный ключ не валиден для текущего месторасположения скрипта.',
						'missing_license_file' => "Ошибка: Пожалуйста, создайте следующий файл (и папки, если они не существуют):<br />\r\n<br />\r\n",
						'license_file_not_writable' => 'Ошибка: Пожалуйста, поставьте права записи на файл(ы):<br />',
						'invalid_local_key_storage' => 'Error: I could not determine the local key storage on clear.',
						'could_not_save_local_key' => 'Ошибка: Не получается сохранить локальный ключ.',
						'license_key_string_mismatch' => 'Ошибка: Локальный ключ не прошел валидацию для данной лицензии.',
						);

		// replace plain text messages with tags, make the tags keys for this localization array on the server side.
		// move all plain text messages to tags & localizations
		$this->localization=array(
						'active' => 'This license is active.', 
						'suspended' => 'Error: This license has been suspended.', 
						'expired' => 'Error: This license has expired.', 
						'pending' => 'Error: This license is pending review.', 
						'download_access_expired' => 'Error: This version of the software was released '.
													 'after your download access expired. Please '.
													 'downgrade or contact support for more information.', 
						);
	}


    function site_id($id, $lkey, $secret_key)
	{
		$this->site_id = $id;
		$this->license_key = $lkey;
		$this->secret_key = $secret_key;
		
		$this->read_query="SELECT `if_enum` as local_key 
			FROM `site_vars` 
			WHERE `name` = 'sys_licence' 
				AND (`site_id` = '0' OR `site_id` = '".$id."') 
			ORDER BY `site_id` DESC 
			LIMIT 0,1
		";  
		
		$this->update_query="UPDATE `site_vars` 
			SET `if_enum`='{local_key}' 
			WHERE `name`='sys_licence' 
				AND (`site_id` = '0' OR `site_id` = '".$id."') 
		";
		$this->initiate = 1;
		return; 
    }

	/**
	* Validate the license
	* 
	* @return string
	*/
	//Главная функция. Она вызывается из файла конфигурации.
	function validate()
	{
		$this->protect();

		// Make sure we have a license key.
		// Убедимся, что в конфиге у нас вообще есть лицензионный ключ
		if (!$this->license_key) 
		{
			return $this->errors=$this->status_messages['missing_license_key']; 
		}

		// Make sure we have a valid local key type.
		// Убедимся, что у нас валидный тип ключа (я так и не понял зачем это нужно. Валидируются две настройки, указанные выше)
		if (!in_array(strtolower($this->local_key_type), $this->valid_local_key_types))
		{ 
			return $this->errors=$this->status_messages['unknown_local_key_type'];
		}

		// Read in the local key.
		// Читаем локальный ключ.		
		switch($this->local_key_storage)
		{
			case 'database':
				$local_key=$this->db_read_local_key();
				break;

			case 'filesystem':
				$local_key=$this->read_local_key(); 
				break;

			default:
				return $this->errors=$this->status_messages['missing_license_key'];
		}

		// Did reading in the local key go ok?
		// Ключ читается хорошо? Если есть ошибки - возвращаем их.
		if ($this->errors) 
		{ 
			return $this->errors; 
		}

		// Validate the local key.
		// И наконец валидируем локальный ключ.
		return $this->validate_local_key($local_key); 
	}

	/**
	* Validate the local license key.
	* 
	* @param string $local_key 
	* @return string
	*/
	function decode_key($local_key)
	{
		return base64_decode(str_replace("\n", '', urldecode($local_key)));
	}

	/**
	* Validate the local license key.
	* 
	* @param string $local_key 
	* @param string $token		{spbas} or \n\n 
	* @return string
	*/
	function split_key($local_key, $token='{license}')
	{
		return explode($token, $local_key);
	}

	// Вытаскиваем массив настроек из ключа
	function get_massiv($local_key, $token='|')
	{
		return explode($token, $local_key);
	}

	/**
	* Does the key match anything valid?
	* 
	* @param string $key
	* @param array $valid_accesses
	* @return array
	*/ 
	function validate_access($key, $valid_accesses)
	{
		return in_array($key, (array)$valid_accesses);
	}

	/**
	* Create an array of wildcard IP addresses
	* 
	* @param string $key
	* @param array $valid_accesses
	* @return array
	*/ 
	function wildcard_ip($key)
	{
		$octets=explode('.', $key);

		array_pop($octets);
		$ip_range[]=implode('.', $octets).'.*';

		array_pop($octets);
		$ip_range[]=implode('.', $octets).'.*';

		array_pop($octets);
		$ip_range[]=implode('.', $octets).'.*';

		return $ip_range;
	}

	/**
	* Create an array of wildcard IP addresses
	* 
	* @param string $key
	* @param array $valid_accesses
	* @return array
	*/ 
	function wildcard_domain($key)
	{
		return '*.'.str_replace('www.', '', $key);
	}

	/**
	* Create a wildcard server hostname
	* 
	* @param string $key
	* @param array $valid_accesses
	* @return array
	*/ 
	function wildcard_server_hostname($key)
	{
		$hostname=explode('.', $key);
		unset($hostname[0]);

		$hostname=(!isset($hostname[1]))?array($key):$hostname;

		return '*.'.implode('.', $hostname);
	}

	/**
	* Extract a specific set of access details from the instance
	* 
	* @param array $instances
	* @param string $enforce
	* @return array
	*/ 
	function extract_access_set($instances, $enforce)
	{
		foreach ($instances as $key => $instance)
		{
			if ($key!=$enforce) { continue; }
			return $instance;
		}

		return array();
	}

	/**
	* Validate the local license key.
	* 
	* @param string $local_key 
	* @return string
	*/
	// Пошла функция валидации локального ключа. Он сюда передается и дрочится на всевозможные несостыковки
	function validate_local_key($local_key)
	{
		// Convert the license into a usable form.
		// Декодируем ключ в читабельную форму.		
		$local_key_src=$this->decode_key($local_key); 

		// Break the key into parts.
		// Разбиваем ключ на части.
		$parts=$this->split_key($local_key_src); 

		// If we don't have all the required parts then we can't validate the key.
		// Если у нас нет всех обязательных частей, то мы не можем валидировать ключ.
		if (!isset($parts[1]))
		{
			return $this->errors=$this->status_messages['local_key_tampering'];
		}

		// Make sure the data wasn't forged.
		// Убедимся, что данные не были подделаны (часть локального ключа засаливается с секретным ключем и сравнивается с другой частью локального ключа, уже засоленной)		
		if(md5($parts[0].$this->secret_key) != $parts[1])
		{
			return $this->errors=$this->status_messages['local_key_tampering'];
		}

		// The local key data in usable form.
		// Приводим данные локального ключа в юзабельную форму
		$parts=$this->get_massiv($local_key_src);
		
		$key_data=unserialize($parts[1]); 
		$instance=$key_data['instance']; unset($key_data['instance']);
		$enforce=$key_data['enforce']; unset($key_data['enforce']); 
		$this->key_data=$key_data; 

		// Make sure this local key is valid for the license key string
		// Убедимся, что локальный ключ (который тоже есть в текстовом файле) такой же, как и лицензионный ключ
		
		if ((string)$key_data['license_key_string']!=(string)$this->license_key)
		{
			return $this->errors=$this->status_messages['license_key_string_mismatch'];
		}

		// Local key expiration check
		// Проверяем, не истек ли срок действия локального ключа		
		if ((string)$key_data['check_period']<time())
		{
			// It's absolutely expired, go remote for a new key!
			// Если истек, то чистим ключ и начинаем получать новый (или что-то вроде того)				
			$this->clear_cache_local_key(true); 
			return $this->validate();
		}

		// Is this key valid for this location?
		// Валиден ли ключ для данного местонахождения скрипта?
		$conflicts=array(); 
		$access_details=$this->access_details();
		foreach ((array)$enforce as $key)
		{
			$valid_accesses=$this->extract_access_set($instance, $key);
			if (!$this->validate_access($access_details[$key], $valid_accesses))
			{
				$conflicts[$key]=true; 
				// check for wildcards
				// Проверка для масок ( я так понял он перебирает все возможные маски ip если есть, а если нет, то что-то делает с доменом и сервер-хостнеймом. И в случае успеха уничтожает переменную конфликтов )
				if (in_array($key, array('ip', 'server_ip')))
				{
					foreach ($this->wildcard_ip($access_details[$key]) as $ip) 
					{
						if ($this->validate_access($ip, $valid_accesses))
						{
							unset($conflicts[$key]);
							break;
						}
					}
				}
				elseif (in_array($key, array('domain')))
				{
					if ($this->validate_access($this->wildcard_domain($access_details[$key]) , $valid_accesses))
					{
						unset($conflicts[$key]); 
					}
				}
				elseif (in_array($key, array('server_hostname')))
				{
					if ($this->validate_access($this->wildcard_server_hostname($access_details[$key]) , $valid_accesses))
					{
						unset($conflicts[$key]);
					}
				}
			}
		} // Конец foreach
					
		// Is the local key valid for this location?
		// Валиден ли локальный ключ для данного местонахождения скрипта?
		if (!empty($conflicts))
		{
			return $this->errors=$this->status_messages['local_key_invalid_for_location'];
		}
			
	} //Конец функции

	
	function db_read_local_key()
	{
		global $db;
		$old = $db->cache_queries;
		$db->cache_queries = false;
		$result = $db->get_row($this->read_query, ARRAY_A);
		$db->cache_queries = $old;
		if(!$result || $db->num_rows == 0) { return $this -> errors="Error 406"; }
			
		// is the local key empty?
		if(empty($result['local_key']))
		{ 
			// Yes, fetch a new local key.
			$result['local_key']=$this->fetch_new_local_key();
				
			// did fetching the new key go ok?
			if ($this->errors) { return $this->errors; }
				
			// Write the new local key.
			$this->db_write_local_key($result['local_key']);
		}
 
		// return the local key
		$r = $result['local_key'];
		$arr = explode('@@@', $r);
		$t = isset($arr[1]) ? $arr[1] : time()-600;
		$this->payd_till = $t;
		return $this->local_key_last=$arr[0];
	}

	/**
	* Write the local key to the database.
	* 
	* @return string|boolean string on error; boolean true on success
	*/
	function db_write_local_key($local_key)
	{
		global $db;
		$db->query(str_replace('{local_key}', $local_key, $this->update_query));
		if(!empty($db->last_error)){ return $this -> errors="Error 434"; }
		return true;
	}
	
	/**
	* Read in the local license key.
	* 
	* @return string
	*/
	// Функция считывания локального ключа из файла
	function read_local_key()
	{ 
		if (!file_exists($path="{$this->local_key_path}{$this->local_key_name}"))
		{
			return $this -> errors=$this->status_messages['missing_license_file'].$path;
		}

		if (!is_writable($path))
		{
			return $this -> errors=$this->status_messages['license_file_not_writable'].$path;
		}

		// is the local key empty?
		if (!$local_key=@file_get_contents($path))
		{
			// Yes, fetch a new local key.
			$local_key=$this->fetch_new_local_key();

			// did fetching the new key go ok?
			if ($this->errors) { return $this->errors; }

			// Write the new local key.
			$this->write_local_key(urldecode($local_key), $path);
		} 
			
		// return the local key
		$r = $local_key;
		$arr = explode('@@@', $r);
		$t = isset($arr[1]) ? $arr[1] : time()-600;
		$this->payd_till = $t;
		return $this->local_key_last=$arr[0];
		//return $this->local_key_last=$local_key;
	}

	/**
	* Clear the local key file cache by passing in ?clear_local_key_cache=y
	* 
	* @param boolean $clear 
	* @return string on error
	*/
	function clear_cache_local_key($clear=false)
	{
		switch(strtolower($this->local_key_storage))
		{
			case 'database':
			$this->db_write_local_key('');
			break;

			case 'filesystem':
			$this->write_local_key('', "{$this->local_key_path}{$this->local_key_name}");
			break;

			default:
			return $this -> errors=$this->status_messages['invalid_local_key_storage'];
		}
	}

	/**
	* Write the local key to a file for caching.
	* 
	* @param string $local_key 
	* @param string $path 
	* @return string|boolean string on error; boolean true on success
	*/
	function write_local_key($local_key, $path)
	{
		$fp=@fopen($path, 'w');
		if (!$fp) { return $this -> errors=$this->status_messages['could_not_save_local_key']; }
		@fwrite($fp, $local_key);
		@fclose($fp);

		return true;
	}

	/**
	* Query the API for a new local key
	*  
	* @return string|false string local key on success; boolean false on failure.
	*/
	function fetch_new_local_key()
	{
		// build a querystring
		$querystring="license_key={$this->license_key}&";
		$querystring.=$this->build_querystring($this->access_details());

		// was there an error building the access details?
		if ($this->errors) { return false; }

		$priority=$this->local_key_transport_order;

		while (strlen($priority)) 
		{
			$use=substr($priority, 0, 1);
			// try fsockopen()
			if ($use=='s') 
			{ 
				if ($result=$this->use_fsockopen($this->api_server, $querystring))
				{
					break;
				}
			}

			// try curl()
			if ($use=='c') 
			{
				if ($result=$this->use_curl($this->api_server, $querystring))
				{
					break;
				}
			}

			// try fopen()
			if ($use=='f') 
			{ 
				if ($result=$this->use_fopen($this->api_server, $querystring))
				{
					break;
				}
			}

			$priority=substr($priority, 1);
		}
		
		return $result;

		if (!$result) 
		{ 
			$this->errors=$this->status_messages['could_not_obtain_local_key']; 
			return false;
		}

		if (substr($result, 0, 7)=='Invalid') 
		{ 
			$this->errors=str_replace('Invalid', 'Error', $result); 
			return false;
		}

		if (substr($result, 0, 5)=='Error') 
		{ 
			$this->errors=$result; 
			return false;
		}

		return $result;
	}

	/**
	* Convert an array to querystring key/value pairs
	* 
	* @param array $array 
	* @return string
	*/
	function build_querystring($array)
	{
		$buffer='';
		foreach ((array)$array as $key => $value)
		{
			if ($buffer) { $buffer.='&'; }
			$buffer.="{$key}={$value}";
		}

		return $buffer;
	}

	/**
	* Build an array of access details
	* 
	* @return array
	*/
	function access_details()
	{
		$access_details=array();

		// Try phpinfo()
		if (function_exists('phpinfo'))
		{
			ob_start();
			phpinfo();
			$phpinfo=ob_get_contents();
			ob_end_clean();

			$list=strip_tags($phpinfo);
			$access_details['domain']=$this->scrape_phpinfo($list, 'HTTP_HOST');
			$access_details['ip']=$this->scrape_phpinfo($list, 'SERVER_ADDR');
			$access_details['directory']=$this->scrape_phpinfo($list, 'SCRIPT_FILENAME');
			$access_details['server_hostname']=$this->scrape_phpinfo($list, 'System');
			$access_details['server_ip']=@gethostbyname($access_details['server_hostname']);
		}

		// Try legacy.
		$access_details['domain']=($access_details['domain'])?$access_details['domain']:$_SERVER['HTTP_HOST'];
		$access_details['ip']=($access_details['ip'])?$access_details['ip']:$this->server_addr();
		$access_details['directory']=($access_details['directory'])?$access_details['directory']:$this->path_translated();
		$access_details['server_hostname']=($access_details['server_hostname'])?$access_details['server_hostname']:@gethostbyaddr($access_details['ip']);
		$access_details['server_hostname']=($access_details['server_hostname'])?$access_details['server_hostname']:'Unknown';
		$access_details['server_ip']=($access_details['server_ip'])?$access_details['server_ip']:@gethostbyaddr($access_details['ip']);
		$access_details['server_ip']=($access_details['server_ip'])?$access_details['server_ip']:'Unknown';

		// Last resort, send something in...
		foreach ($access_details as $key => $value)
		{
			$access_details[$key]=($access_details[$key])?$access_details[$key]:'Unknown';
		}

		// enforce product IDs
		if ($this->valid_for_product_tiers)
		{
			$access_details['valid_for_product_tiers']=$this->valid_for_product_tiers;
		}
		return $access_details;
	}

	/**
	* Get the directory path
	* 
	* @return string|boolean string on success; boolean on failure
	*/
	function path_translated()
	{
		$option=array('PATH_TRANSLATED', 
					'ORIG_PATH_TRANSLATED', 
					'SCRIPT_FILENAME', 
					'DOCUMENT_ROOT',
					'APPL_PHYSICAL_PATH');

		foreach ($option as $key)
		{
			if (!isset($_SERVER[$key])||strlen(trim($_SERVER[$key]))<=0) { 
				continue; 
			}

			if ($this->is_windows()&&strpos($_SERVER[$key], '\\')){
				return  @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], '\\'));
			}
			
			return  @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], '/'));
		}

		return false;
	}

	/**
	* Get the server IP address
	* 
	* @return string|boolean string on success; boolean on failure
	*/
	function server_addr()
	{
		$options=array('SERVER_ADDR', 'LOCAL_ADDR');
		foreach ($options as $key)
		{
			if (isset($_SERVER[$key])) { return $_SERVER[$key]; }
		}

		return false;
	}

	/**
	* Get access details from phpinfo()
	* 
	* @param array $all 
	* @param string $target
	* @return string|boolean string on success; boolean on failure
	*/
	function scrape_phpinfo($all, $target)
	{
		$all=explode($target, $all);
		if (count($all)<2) { return false; }
		$all=explode("\n", $all[1]);
		$all=trim($all[0]);

		if ($target=='System')
		{
			$all=explode(" ", $all);
			$all=trim($all[(strtolower($all[0])=='windows'&&strtolower($all[1])=='nt')?2:1]);
		}

		if ($target=='SCRIPT_FILENAME')
		{
			$slash=($this->is_windows()?'\\':'/');

			$all=explode($slash, $all);
			array_pop($all);
			$all=implode($slash, $all);
		}

		if (substr($all, 1, 1)==']') { return false; }

		return $all;
	}

	/**
	* Pass the access details in using fsockopen
	* 
	* @param string $url 
	* @param string $querystring
	* @return string|boolean string on success; boolean on failure
	*/
	function use_fsockopen($url, $querystring)
	{
		if (!function_exists('fsockopen')) { return false; }
		$url=parse_url($url);

		if(empty($url['scheme'])){ $url['scheme'] = 'http'; }
		if($url['scheme'] == 'https'){
			$host2 = 'ssl://'.$url['host'];
			$this->remote_port = 443;
		}else{
			$host2 = $url['host'];
			$this->remote_port = 80;
		}
		
		$fp=@fsockopen($host2, $this->remote_port, $errno, $errstr, $this->remote_timeout);
		if (!$fp) { return false; }

		$header="POST {$url['path']} HTTP/1.0\r\n";
		$header.="Host: {$url['host']}\r\n";
		$header.="Content-type: application/x-www-form-urlencoded\r\n";
		$header.="User-Agent: Simpla.es (http://www.simpla.es)\r\n";
		$header.="Content-length: ".@strlen($querystring)."\r\n";
		$header.="Connection: close\r\n\r\n";
		$header.=$querystring;

		$result=false;
		fputs($fp, $header);
		while (!feof($fp)) { $result.=fgets($fp, 1024); }
		fclose ($fp);
		
		if (strpos($result, '200')===false) { return false; }
		$result=explode("\r\n\r\n", $result, 2);
		if (!$result[1]) { return false; }
		return $result[1];
	}

	/**
	* Pass the access details in using cURL
	* 
	* @param string $url 
	* @param string $querystring
	* @return string|boolean string on success; boolean on failure
	*/
	function use_curl($url, $querystring)
	{
		if (!function_exists('curl_init')) { return false; }
		$curl = curl_init();
		
		$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Keep-Alive: 300";
		$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header[] = "Accept-Language: en-us,en;q=0.5";
		$header[] = "Pragma: ";
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Simpla.es (http://www.simpla.es)');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->remote_timeout);
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->remote_timeout); // 60

		$result= curl_exec($curl);
		$info=curl_getinfo($curl);
		curl_close($curl);

		if ((integer)$info['http_code']!=200) { return false; }

		return $result;
	}

	/**
	* Pass the access details in using the fopen wrapper file_get_contents()
	* 
	* @param string $url 
	* @param string $querystring
	* @return string|boolean string on success; boolean on failure
	*/
	function use_fopen($url, $querystring)
	{ 
		if (!function_exists('file_get_contents')) { return false; }
		return @file_get_contents("{$url}?{$querystring}");
	}

	/**
	* Determine if we are running windows or not.
	* 
	* @return boolean
	*/
	function is_windows()
	{
		return (strtolower(substr(php_uname(), 0, 7))=='windows'); 
	}


		
}
?>