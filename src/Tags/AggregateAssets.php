<?php


namespace Gioppy\StatamicAggregateAssets\Tags;


use Statamic\Tags\Tags;

class AggregateAssets extends Tags {

  protected static $aliases = ['aggregate'];

  /**
   * Parse anche aggregate JavaScript files
   *
   * @return mixed
   */
  public function js() {
    $preContent = '[JS]';
    $postContent = '[/JS]';
    $this->setContent($preContent . $this->content . $postContent);
    return $this->content;
  }

  /**
   * Parse anche aggregate Stylesheet files
   *
   * @return mixed
   */
  public function css() {
    $preContent = '[CSS]';
    $postContent = '[/CSS]';
    $this->setContent($preContent . $this->content . $postContent);
    return $this->content;
  }
}
