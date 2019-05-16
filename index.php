<?php
	/**
	 * Getting the Google news feed JSON
	 * 
	 * When you instance the GoogleNews you can send the params
	 * inside of the Object like this:
	 * 		$news = new GoogleNews(
	 * 				[
	 * 					'search' => 'Deportes',
	 * 					'numberOfNews' => 5,
	 * 					'newsWithImages' => true,
	 * 					'countryCode' => 'AR',
	 * 					'language' => 'es',
	 * 				]
	 * 		);
	 * 
	 * 	or setting the params one by one
	 */
	include('GoogleNews.php');

	$news = new GoogleNews();
	$news->setSearchQuery('Deportes');
	$news->setNumberOfNews(5);
	$news->setNewsWithImages(true);
	$news->setCountryCode('AR');
	$news->setLanguage('es');

	// Printing the news
	print_r($news->getNews());
?>