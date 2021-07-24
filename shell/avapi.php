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

	public function getEndpoint($endpoint = ''){
		return $this->_endpoint . $endpoint;
	}

	public function getAllProductCollections(){
		$collections = $this->getContentByCRUL($this->getEndpoint('/productcollections') . '?_limit=1000');

		// $collections = file_get_contents('../static/sample.json');
		$collections = json_decode($collections, true);
		return $collections;
	}

	public function generateProductCollection(){
		$colletions = $this->getAllProductCollections();
		
		if(!count($colletions)) return null;

		foreach ($colletions as $collection) {
			// var_dump($colletions);

			//1. Generate collection
			//template
			$this->templateFile = '_templates/productcollection.html';
			$this->outputFolder = '../_productcollections';
			$fields = [
				'id' => $collection['_id'],
				'title' => trim(strip_tags($collection['title'])),
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'sort_order' =>  $collection['sort_order'],
				'image' =>  $collection['image'],
				'permalink' =>  $collection['permalink'],
				'description' =>  trim(strip_tags($collection['description'])),
				'meta_title' =>  trim(strip_tags($collection['meta_title'])),
				'h1_title' =>  trim(strip_tags($collection['h1_title'])),
				'meta_description' =>  trim(strip_tags($collection['meta_description'])),
				'content' =>  $collection['content'],
				'createdAt' =>  $collection['createdAt'],
				'updatedAt' =>  $collection['updatedAt'],
				'author' =>  $collection['author']['name'],
				'author_key' =>  $collection['author']['key'],
				'category.id' =>  $collection['productcategory']['_id'],
				'category.title' => $collection['productcategory']['title'],
				'category.permalink' =>  $collection['productcategory']['permalink'],
				'topic.id' =>  $collection['producttopic']['_id'],
				'topic.title' => $collection['producttopic']['title'],
				'topic.permalink' =>  $collection['producttopic']['permalink'],
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
				$item['image'] = $item['image'];
				$this->setVars($item);
				$this->tag = $item['title'];
				$this->generateFile($this->getFileName($collection['id'].'-'.$item['title']));

			}


		}

	}

	public function generateCategories(){
		$collections = $this->getContentByCRUL($this->getEndpoint('/productcategories') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No categories found";
			return;
		};

		foreach ($collections as $collection) {

			$this->templateFile = '_templates/productcategories.html';
			$this->outputFolder = '../_productcategories';
			$fields = [
				'id' => $collection['_id'],
				'title' => trim(strip_tags($collection['title'])),
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'slug' =>  $this->slug($collection['title']),
				'content' =>  $collection['content'],
				'createdAt' =>  $collection['createdAt'],
				'updatedAt' =>  $collection['updatedAt'],
				'permalink' =>  $collection['permalink'],
			];

			$this->setVars($fields);
			$this->tag = $collection['title'];
			$this->generateFile($this->getFileName($collection['title']));

		}
	}


	public function generateTopics(){
		$collections = $this->getContentByCRUL($this->getEndpoint('/producttopics') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No topics found";
			return;
		};

		foreach ($collections as $collection) {

			$this->templateFile = '_templates/producttopics.html';
			$this->outputFolder = '../_producttopics';
			$fields = [
				'id' => $collection['_id'],
				'title' => trim(strip_tags($collection['title'])),
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'slug' =>  $this->slug($collection['title']),
				'content' =>  $collection['content'],
				'createdAt' =>  $collection['createdAt'],
				'updatedAt' =>  $collection['updatedAt'],
				'permalink' =>  $collection['permalink'],
			];

			$this->setVars($fields);
			$this->tag = $collection['title'];
			$this->generateFile($this->getFileName($collection['title']));

		}
	}


	public function generateAuthors(){
		$collections = $this->getContentByCRUL($this->getEndpoint('/authors') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No topics found";
			return;
		};

		foreach ($collections as $collection) {

			$this->templateFile = '_templates/authors.html';
			$this->outputFolder = '../_authors';
			$fields = [
				'id' => $collection['_id'],
				'title' => trim(strip_tags($collection['name'])),
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'key' =>  $collection['key'],
				'content' =>  $collection['content'],
				'createdAt' =>  $collection['createdAt'],
				'updatedAt' =>  $collection['updatedAt'],
				'permalink' =>  '/author/'.$collection['key'] . '/',
			];

			$this->setVars($fields);
			$this->tag = $collection['name'];
			$this->generateFile($this->getFileName($collection['name']));

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
		$this->generateCategories();
		$this->generateTopics();
		$this->generateAuthors();
	}


}



//Generate pair integration
$shell = new Sam();
$apps = $shell->run();
