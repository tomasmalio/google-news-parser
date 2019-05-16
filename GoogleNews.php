<?php
	/**
	 * Google-News feed parser and JSON provider Class
	 * Github <https://github.com/tomasmalio>
	 * 
	 * @author Tomas Malio <tomasmalio@gmail.com>
	 */
	class ModelNewsGoogle {
		// Google news url RSS
		public $url = 'https://news.google.com/rss/news?q=';
		// Keyword to get info
		public $searchQuery = 'Good News';
		// Number of news to get
		public $numberOfNews;
		// Country code
		public $countryCode = 'AR';
		// Language
		public $language = 'es';
		// News always with image
		public $newsWithImages = false;

		public function __construct ($params = []) {
			self::setSearchQuery($params['search']);
			self::setNumberOfNews($params['numberOfNews']);
			self::setNewsWithImages($params['newsWithImages']);
			self::setCountryCode($params['countryCode']);
			self::setLanguage($params['language']);
		}

		public function setSearchQuery ($searchQuery) {
			if (!empty($searchQuery)) {
				$this->searchQuery = $searchQuery;
			}		
		}

		public function setNumberOfNews ($numberOfNews) {
			if (!empty($numberOfNews)) {
				$this->numberOfNews = (int) $numberOfNews;
			}
		}
		public function setNewsWithImages ($newsWithImages) {
			if ($newsWithImages) {
				$this->newsWithImages = $newsWithImages;
			}
		}

		public function setCountryCode ($countryCode) {
			if (!empty($countryCode)) {
				$this->countryCode = $countryCode;
			}		
		}

		public function setLanguage ($language) {
			if (!empty($language)) {
				$this->language = $language;
			}		
		}

		public function getNews () {
			return $this->processNews();
		}

		private function processNews() {
			$rss = simplexml_load_file($this->url . $this->searchQuery.'&hl='.$this->language.'&gl='.$this->countryCode.'&ceid='.$this->countryCode.':'.$this->language);
			$namespaces = $rss->getNamespaces(true);
			
			$news = [];
			$i = 0;
			$withImages = 0;
			foreach ($rss->channel->item as $item) {
				$media_content = $item->children($namespaces['media']);

				foreach($media_content as $j){
					$image = (string)$j->attributes()->url;
				}

				// Formating the news content
				$description = (explode('<p>', $item->description))[1];
				$source =  explode('<p>', str_replace('<font color="#6f6f6f">', '</font>', (explode('<font color="#6f6f6f">', $item->description))[1]));
				$source = $source[0];
				$title = (string)$item->title;
				$pos = strpos($item->title, $source);
				if ($pos !== false) {
					$title = (string)$item->title;
				} else {
					$title = (explode('-', (string)$item->title))[0];
				}

				/**
				 * Generate the array with the info
				 * 
				 * It's important that if newsWithImages it's set in true and the news received
				 * doesn't have image, the array is not going to be added.
				 **/
				if ((isset($this->newsWithImages) && $this->newsWithImages && $image) || !isset($this->newsWithImages)) {
					$news[$i]['id'] = (string)$item->guid;
					$news[$i]['title'] = $title;
					$news[$i]['description'] = $description;
					$news[$i]['image'] = $image;
					$news[$i]['source'] = $source;
					$news[$i]['link'] = (string)$item->link;
					$news[$i]['datetime'] = date('Y-m-d H:i:s', strtotime($item->pubDate));
					$i++;
				}
				
				if ($i == $this->numberOfNews){
					break;
				}
				unset($image);
			}
			return json_encode($news);
		}
	}
?>