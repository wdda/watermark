<?php
/* 
	PHP Watermark
	Author Alferov D. WDDA
	https://github.com/wdda/watermark
*/

//На всякий случай запретим выполнять без пароля (его нужно поменять в .htaccess)
if(!empty($_GET['pass'])){
	
	//Поменяйте в htaccess и тут
	if($_GET['pass'] != '123123') die;
	
}else{die;}

//Путь до файла с оригинальным изображением
$path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
$nameImage = end(explode('/', $_SERVER['REQUEST_URI'])); //Имя изображения
$nameImageId = md5($path) . '_' . $nameImage; //Имя изображения в кеше

//Проверяем дату для рефреша кеша
if(file_exists('cache/' . $nameImageId)){
	
	//Время создания файла в кеше
	$dateImageCache = filemtime('cache/' . $nameImageId);
	
	//Время создания оригинального файла
	$dateImage = filemtime($path);
	
	//Если оригинал старше чем в кеше
	if($dateImage < $dateImageCache){
		
		$image = new Imagick();
		$image->readImage('cache/' . $nameImageId);
		header('Content-type: image/jpeg');
		echo $image->getImageBlob();
		
	}else{ newImage($path, $nameImageId); }
	
}else{ newImage($path, $nameImageId); }

//Если нет в кеше или есть но более старая версия
function newImage($path, $nameImageId){
	// Загружаем оригинальное изображение 
	$image = new Imagick();
	$image->readImage($path);
	$w = $image->getImageWidth(); 
	$h = $image->getImageHeight();
	
	$imageWatermark = new Imagick();
	$imageWatermark->readImage('watermark.png');
	$ww = $imageWatermark->getImageWidth(); 
	$wh = $imageWatermark->getImageHeight();
	
	//Отступ снизу
	$paddingBottom = 10;
	
	//Отступ справа
	$paddingRight = 10;
	
	//Это позволяет поставить изображение в нижний правый угол (учитывая отступы)
	$x = ($w - $ww) - $paddingRight;
	$y = ($h - $wh) - $paddingBottom;
	
	$image->compositeImage($imageWatermark, imagick::COMPOSITE_OVER, $x, $y);
	$image->writeImage('cache/' . $nameImageId);
	header('Content-type: image/jpeg');
	echo $image->getImageBlob();
}