<?php

class TUFSearchResult {
  const TUF_SEARCH_RESULT_BLOG = 'blog';
  const TUF_SEARCH_RESULT_PRODUCT = 'product';
  const TUF_SEARCH_RESULT_TRAINING = 'training';
  const TUF_SEARCH_RESULT_TEACHER = 'teacher';
  const TUF_SEARCH_RESULT_FAQ = 'faq';
  const TUF_SEARCH_RESULT_WP_VIDEO_CAT_ID = 856;

  private $category;
  private $title;
  private $teaser;
  private $tag;
  private $url;

  /**
   * @return mixed
   */
  public function getCategory() {
    return $this->category;
  }

  /**
   * @param mixed $category
   */
  public function setCategory($category) {
    $this->category = $category;
  }

  /**
   * @return mixed
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @return mixed
   */
  public function getTeaser() {
    return $this->teaser;
  }

  /**
   * @param mixed $teaser
   */
  public function setTeaser($teaser) {
    $this->teaser = $teaser;
  }

  /**
   * @return mixed
   */
  public function getTag() {
    return $this->tag;
  }

  /**
   * @param mixed $tag
   */
  public function setTag($tag) {
    $this->tag = $tag;
  }

  /**
   * @return mixed
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * @param mixed $url
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  public function getRenderArray(){
    return array(
      '#theme' => 'tuf_search_result',
      '#title' => $this->getTitle(),
      '#teaser' => $this->getTeaser(),
      '#tag' => $this->getTag(),
      '#url' => $this->getUrl(),
    );
  }

  /**
   * @param $result
   * @return TUFSearchResult
   */
  public static function createFromResultArray($result){
    $searchResult = new TUFSearchResult();
    $searchResult->setCategory($searchResult->getCategoryFromResult($result));
    $searchResult->setTitle($result['title']);
    $searchResult->setTeaser($result['snippet']);
    $searchResult->setUrl($result['link']);
    $searchResult->setTag($result['tag']);

    return $searchResult;
  }

  /**
   * @param array $result
   * @return string
   */
  private function getCategoryFromResult($result){
    if(isset($result['node'])){
      switch($result['node']->type){
        case 'faq':
          return TUFSearchResult::TUF_SEARCH_RESULT_FAQ;
          break;
        case 'product':
        case 'quickfix_video':
          return TUFSearchResult::TUF_SEARCH_RESULT_PRODUCT;
          break;
        case 'class':
          return TUFSearchResult::TUF_SEARCH_RESULT_TRAINING;
          break;
        case 'teacher':
          return TUFSearchResult::TUF_SEARCH_RESULT_TEACHER;
          break;
      }
    }
    else{
      return TUFSearchResult::TUF_SEARCH_RESULT_BLOG;
    }

    return '';
  }

}