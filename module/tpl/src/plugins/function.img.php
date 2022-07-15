<?php

/*
 * Simpla plugin 04.01.2019
 * -------------------------------------------------------------
 * Type:     function
 * Name:     img
 * Purpose:  show cached image or create it 
 *
 * Params:
 * url
 * width & height - find out cached image
 * ... or create image with sent sizes
 * assign - name in template to use it later (default 'img')
 * folder - folder where the image
 *
 * return array =>
 * path, src, width, height, mime, sizes
 *
 * -------------------------------------------------------------
 */
 
function tpl_function_img($params, &$template_object)
{
	global $tpl;
	// $tpl->_vars['site_vars']['site_url']
	
	if(empty($params['url']))
		return;
	
	if(!isset($params['width']))
		$params['width'] = isset($params['width']) ? intval($params['width']): 0;
	
	if(!isset($params['height']))
		$params['height'] = isset($params['height']) ? intval($params['height']): 0;
		
	if(empty($params['folder']))
		$params['folder'] = '/upload/images/';
	
	// compatible with old version
	if(defined('ABS_PATH') AND !defined('PATH')) 
		define('PATH', ABS_PATH);
	
	$f_path = PATH.$params['url'];
	$f_url = $params['url'];
	$f_file = basename($params['url']);
	
	if(!file_exists($f_path))
		return;
	
	$size = getimagesize($f_path);
	$arResult = array(
		'path' => $f_path,
		'src' => $f_url,
		'width' => $size[0],
		'height' => $size[1],
		'mime' => $size['mime'],
		'sizes' => $size[3],
	);
		
	if($params['width'] < 1 && $params['height'] < 1){
		//return $arResult;
	}elseif($params['width'] > $arResult['width']){
		//return $arResult;
	}elseif($params['height'] > $arResult['height']){
		//return $arResult;
	}else{
		// check cached image
		$tempFileArray = explode('.', $f_file);
		$extension = end($tempFileArray);
		$start = str_replace('.'.$extension, '', $f_file);
		$start .= '_'.$params['width'].'_'.$params['height'];
		$cachedImage = $start.'.'.$extension;
		$subfolder = substr($start, 0, 2);		
		$cachedImagePath = PATH.$params['folder'].$subfolder.'/'.$cachedImage;
		$cachedImageSRC = $params['folder'].$subfolder.'/'.$cachedImage;
		$subfolderPath = PATH.$params['folder'].$subfolder;
			
		if(!file_exists($cachedImagePath)){
			// create folder if no exists
			if (!file_exists($subfolderPath)) {
				mkdir($subfolderPath, 0777, true);
			}
			
			// create cached image
			$logo = array();
			require_once MODULE.'/resize/AcImage.php';
			$image = AcImage::createImage($f_path);
			AcImage::setRewrite(true);
			$params['width'] = intval($params['width']);
			$params['height'] = intval($params['height']);
			
			if($params['height'] == 0){
				// Если 0 по высоте, то она не имеет значения и вырезаем только по ширине
				$image
            ->resizeByWidth($params['width'])
			->drawLogo($logo, AcImage::BOTTOM_RIGHT)
            ->save($cachedImagePath);
			}elseif($params['width'] == 0){
				// Если 0 по ширине, то она не имеет значения и вырезаем только по высоте
				$image->resizeByHeight($params['height'])->drawLogo($logo, AcImage::BOTTOM_RIGHT)->save($cachedImagePath);
			}else{
				// вычислим соотношение сторон
				$w = $image->getWidth();
				$h = $image->getHeight();
				$test_height = $h*$params['width']/$w;
				
				if($test_height < $params['height']){
				// ->drawLogo($logo, AcImage::BOTTOM_RIGHT) не работает
					$image
						->resizeByHeight($params['height'])
						->cropCenter($params['width'],$params['height'])

						->save($cachedImagePath);
				}else{
				// ->drawLogo($logo, AcImage::BOTTOM_RIGHT) не работает
					$image
						->resizeByWidth($params['width'])
						->cropCenter($params['width'],$params['height'])
						->save($cachedImagePath);
				}
			}
		}
		
		$size = getimagesize($cachedImagePath);
		$arResult = array(
			'path' => $cachedImagePath,
			'src' => $cachedImageSRC,
			'width' => $size[0],
			'height' => $size[1],
			'mime' => $size['mime'],
			'sizes' => $size[3],
		);
	}

	if (empty($params['assign']))
	{
		$template_object->assign('img',$arResult);
	}
	else
	{
		$template_object->assign($params['assign'],$arResult);
	}	
	
}

?>