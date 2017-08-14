<?php

class TUFSearchResults {

  private $searchResultsBlog;
  private $searchResultsProduct;
  private $searchResultsTraining;
  private $searchResultsTeacher;
  private $searchResultsFaq;
  private $totalResultsBlog;
  private $totalResultsProduct;
  private $totalResultsTraining;
  private $totalResultsTeacher;
  private $totalResultsFaq;
  private $searchTerm;

  /**
   * TUFSearchResults constructor.
   * @param $searchTerm
   */
  public function __construct($searchTerm) {
    $this->searchTerm = $searchTerm;
    $this->searchResultsBlog = array();
    $this->searchResultsProduct = array();
    $this->searchResultsTraining = array();
    $this->searchResultsTeacher = array();
    $this->searchResultsFaq = array();
  }

  /**
   * @return TUFSearchResult[]
   */
  public function getSearchResultsBlog() {
    return $this->searchResultsBlog;
  }

  /**
   * @return TUFSearchResult[]
   */
  public function getSearchResultsProduct() {
    return $this->searchResultsProduct;
  }

  /**
   * @return TUFSearchResult[]
   */
  public function getSearchResultsTraining() {
    return $this->searchResultsTraining;
  }

  /**
   * @return TUFSearchResult[]
   */
  public function getSearchResultsTeacher() {
    return $this->searchResultsTeacher;
  }

  /**
   * @return TUFSearchResult[]
   */
  public function getSearchResultsFaq() {
    return $this->searchResultsFaq;
  }

  /**
   * @param mixed $totalResultsBlog
   */
  public function setTotalResultsBlog($totalResultsBlog) {
    $this->totalResultsBlog = $totalResultsBlog;
  }

  /**
   * @param mixed $totalResultsProduct
   */
  public function setTotalResultsProduct($totalResultsProduct) {
    $this->totalResultsProduct = $totalResultsProduct;
  }

  /**
   * @param mixed $totalResultsTraining
   */
  public function setTotalResultsTraining($totalResultsTraining) {
    $this->totalResultsTraining = $totalResultsTraining;
  }

  /**
   * @param mixed $totalResultsTeacher
   */
  public function setTotalResultsTeacher($totalResultsTeacher) {
    $this->totalResultsTeacher = $totalResultsTeacher;
  }

  /**
   * @param mixed $totalResultsFaq
   */
  public function setTotalResultsFaq($totalResultsFaq) {
    $this->totalResultsFaq = $totalResultsFaq;
  }

  /**
   * @return mixed
   */
  public function getSearchTerm() {
    return $this->searchTerm;
  }

  public function addResult(TUFSearchResult $searchResult){
    switch($searchResult->getCategory()){
      case TUFSearchResult::TUF_SEARCH_RESULT_BLOG:
        $this->searchResultsBlog[] = $searchResult;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_PRODUCT:
        $this->searchResultsProduct[] = $searchResult;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TEACHER:
        $this->searchResultsTeacher[] = $searchResult;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TRAINING:
        $this->searchResultsTraining[] = $searchResult;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_FAQ:
        $this->searchResultsFaq[] = $searchResult;
        break;
    }
  }

  public function getTotalResults($category){
    switch($category['code']){
      case TUFSearchResult::TUF_SEARCH_RESULT_BLOG:
        return $this->totalResultsBlog;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_PRODUCT:
        return $this->totalResultsProduct;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TEACHER:
        return $this->totalResultsTeacher;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TRAINING:
        return $this->totalResultsTraining;
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_FAQ:
        return $this->totalResultsFaq;
        break;
    }
  }

  public function getRenderArray($category, $limit = 0){
    return array(
      '#theme' => 'tuf_search_results',
      '#category' => $category,
      '#limit' => $limit,
      '#results' => $this->getResultsRenderArrayForCategory($category),
    );
  }

  public static function getCategoryList(){
    return array(
      TUFSearchResult::TUF_SEARCH_RESULT_BLOG => 'Blogs',
      TUFSearchResult::TUF_SEARCH_RESULT_PRODUCT => 'Products',
      TUFSearchResult::TUF_SEARCH_RESULT_TRAINING => 'Trainings',
      TUFSearchResult::TUF_SEARCH_RESULT_TEACHER => 'Teachers',
      TUFSearchResult::TUF_SEARCH_RESULT_FAQ => 'FAQs',
    );
  }

  private function getResultsForCategory($category){
    switch ($category['code']){
      case TUFSearchResult::TUF_SEARCH_RESULT_BLOG:
        return $this->getSearchResultsBlog();
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_PRODUCT:
        return $this->getSearchResultsProduct();
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TEACHER:
        return $this->getSearchResultsTeacher();
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_TRAINING:
        return $this->getSearchResultsTraining();
        break;
      case TUFSearchResult::TUF_SEARCH_RESULT_FAQ:
        return $this->getSearchResultsFaq();
        break;
    }

    return array();
  }

  private function getResultsRenderArrayForCategory($category){
    $render = array();

    foreach($this->getResultsForCategory($category) as $searchResult){
      $render[] = $searchResult->getRenderArray();
    }

    return $render;
  }
}