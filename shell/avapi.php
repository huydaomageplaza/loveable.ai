<?php
if (!defined('MAGE_ROOT')) {
	define('MAGE_ROOT', getcwd());
}



include MAGE_ROOT . '/abstract.php';

class Sam extends Shell{
	public $tag;
	public $fileName;
	public $_endpoint = 'http://avapi.avada.io:1337';


	public function cleanCollectionFolder($folder){
		$path = '../' . $folder;
		//DOTO: Clean up files
	}

	public function getFileName($name){
		return  date("Y-m-d") .'-' . Sam::slug($name) .'.md';
	}

	public function appEndpoint(){
		return $this->_endpoint . '/productcollections';
	}

	public function getAllProductCollections(){
		// $collections = $this->getContentByCRUL($this->appEndpoint() . '?_limit=500');

		$collections = file_get_contents('../static/sample.json');
		$collections = json_decode($collections, true);
		return $collections;
	}

	public function generateProductCollection(){
		


		$colletions = $this->getAllProductCollections();
		foreach ($colletions as $collection) {
			// var_dump($colletions);

			//1. Generate collection

			//template
			$this->templateFile = '_templates/productcollection.html';
			$this->outputFolder = '../_productcollections';
			$fields = [
				'id' => $collection['_id'],
				'title' => $collection['title'],
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'sort_order' =>  $collection['sort_order'],
				'image' =>  'http://avapi.avada.io:1337' . $collection['image']['url'],
				'permalink' =>  $collection['permalink'],
				'description' =>  $collection['description'],
				'metaTitle' =>  $collection['metaTitle'],
				'h1Title' =>  $collection['h1Title'],
				'metaDescription' =>  $collection['metaDescription'],
				'content' =>  $collection['content'],
				'createdAt' =>  $collection['createdAt'],
				'updatedAt' =>  $collection['updatedAt'],
				'author' =>  $collection['author']['name'],
				'category.id' =>  $collection['productcategories'][0]['_id'],
				'category.title' => $collection['productcategories'][0]['title'],
				'category.permalink' =>  $collection['productcategories'][0]['permalink'],
				'item_count' =>  count($collection['productitems']),
			];

			//Generate Items HTML
			// $fields ['items_html'] = $this->getItemHtml($collection['productitems']);
			// $fields ['related_items_html'] = $this->getRelatedItems($collection['relatedItems']);
 
			$this->setVars($fields);
			$this->tag = $collection['title'];
			$this->generateFile($this->getFileName($collection['title']));

			// 2. Generate items

			$this->templateFile = '_templates/productitems.html';
			$this->outputFolder = '../_productitems';

			foreach ($collection['productitems'] as $item){
				$item['collection_id'] = $collection['id'];
				$item['slug'] = $this->slug($item['title']);
				$item['image'] = 'http://avapi.avada.io:1337' . $item['image']['url'];
				$this->setVars($item);
				$this->tag = $item['title'];
				$this->generateFile($this->getFileName($collection['id'].'-'.$item['title']));

			}

		

		}

	}

	public function getItemHtml($items){
		$html = 'Item html here';
		foreach ($items as $item) {
				//generate from template
				
			}

		return $html;
	}

	public function run($debug = false){
		$this->generateProductCollection();
	}


}



//Generate pair integration
$shell = new Sam();
$apps = $shell->run();
