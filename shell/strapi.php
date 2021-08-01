<?php
if (!defined('MAGE_ROOT')) {
	define('MAGE_ROOT', getcwd());
}

error_reporting(0);



include MAGE_ROOT . '/abstract.php';

class Sam extends Shell{
	public $tag;
	public $fileName;
	public $_endpoint = 'https://loveable.appspot.com';


	public function cleanCollectionFolder($folder){
		$path = '../' . $folder;
		//DOTO: Clean up files
	}

	public function getFileName($name, $date){
		return  date("Y-m-d", strtotime($date)) .'-' . Sam::slug($name) .'.md';
	}

	public function getEndpoint($endpoint = ''){
		return $this->_endpoint . $endpoint;
	}

	public function getAllProductCollections($start = 0, $limit = 500){
		$collections = $this->getContentByCRUL(
			$this->getEndpoint('/productcollections') . 
			'?_limit=' . $limit . 
			'&_start=' . $start . 
			'&_published=true'
		);

		// $collections = file_get_contents('../static/sample.json');
		$collections = json_decode($collections, true);
		return $collections;
	}

	public function generateProductCollection(){

		$colletions = $this->getAllProductCollections(0, 500);
		
		if(!count($colletions)) return null;


		//0. Clean up folder _productcollections; _productitems
		$this->cleanUpFolder('../_productcollections');
		$this->cleanUpFolder('../_productitems');

		foreach ($colletions as $collection) {

			//1. Generate collection
			//template
			$this->templateFile = '_templates/productcollection.html';
			$this->outputFolder = '../_productcollections';

			// if($collection['published'] == false) continue;

			$fields = [
				'id' => $collection['_id'],
				'title' => trim(strip_tags($collection['title'])),
				'allow_search_engine' => (string) $collection['allow_search_engine'],
				'published' =>  (string) $collection['published'],
				'sort_order' =>  $collection['sort_order'],
				'image' =>  $collection['media']['url'],
				'srcset' =>  $this->getSrcSet($collection['media']),
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
			$this->generateFile($this->getFileName($collection['title'], $collection['createdAt']));

			// 2. Generate items

			$this->templateFile = '_templates/productitems.html';
			$this->outputFolder = '../_productitems';

			foreach ($collection['productitems'] as $item){
				$item['collection_id'] = $collection['id'];
				$item['slug'] = $this->slug($item['title']);
				$item['image'] = $item['media']['url'];
				$item['srcset'] = $this->getSrcSet($item['media']);
				$this->setVars($item);
				$this->tag = $item['title'];
				$this->generateFile($this->getFileName($collection['id'].'-'.$item['title'], $item['createdAt']));

			}


		}

	}

	public function getSrcSet($media){
		$set = '';
		$i = 1;
		foreach ($media['formats'] as $_format) {


			if($i == count($media['formats'])){ //last one
					$set .= $_format['url'] . ' ' . $_format['width'] . 'w';	
			} else{//next
				$set .= $_format['url'] . ' ' . $_format['width'] . 'w, ';
			}
			$i++;
		}

		return $set;
	}

	public function generateCategories(){

		

		$collections = $this->getContentByCRUL($this->getEndpoint('/productcategories') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No categories found";
			return;
		};


		//0. Clean up folder _productcollections
		$this->cleanUpFolder('../_productcategories');

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
			$this->generateFile($this->getFileName($collection['title'], $collection['createdAt']));

		}
	}


	public function generateTopics(){


		$collections = $this->getContentByCRUL($this->getEndpoint('/producttopics') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No topics found";
			return;
		};


		//0. Clean up folder _productcollections
		$this->cleanUpFolder('../_producttopics');


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
			$this->generateFile($this->getFileName($collection['title'], $collection['createdAt']));

		}
	}


	public function generateAuthors(){

		

		$collections = $this->getContentByCRUL($this->getEndpoint('/authors') . '?_limit=500');
		$collections = json_decode($collections, true);
		if(!count($collections)) {
			echo "No topics found";
			return;
		};

		//0. Clean up folder _productcollections
		$this->cleanUpFolder('../_authors');

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
			$this->generateFile($this->getFileName($collection['name'], $collection['createdAt']));

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
