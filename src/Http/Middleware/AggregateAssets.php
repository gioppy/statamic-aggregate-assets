<?php


namespace Gioppy\StatamicAggregateAssets\Http\Middleware;


use Closure;
use DOMElement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AggregateAssets {

  protected \DOMDocument $dom;

  protected $disk;

  private string $cssRegex = '/\[CSS\](.*?)\[\/CSS\]/s';

  private string $jsRegex = '/\[JS\](.*?)\[\/JS\]/s';

  public function __construct() {
    $this->dom = new \DOMDocument();

    $this->disk = Storage::disk('aggregated');
  }

  public function handle(Request $request, Closure $next) {
    /**
     * @var Response $response
     */
    $response = $next($request);

    if ($this->isResponse($response) && $this->isHtml($response)) {
      $html = $response->getContent();

      $cssFilePath = $this->css($html);
      $html = preg_replace($this->cssRegex, '<link rel="stylesheet" href="' . $this->disk->url($cssFilePath) . '" />', $html);

      $jsFilePath = $this->js($html);
      $html = preg_replace($this->jsRegex, '<script src="' . $this->disk->url($jsFilePath) . '"></script>', $html);

      $response->setContent($html);
    }

    return $response;
  }

  /**
   * Check if the response is a valid Response object
   *
   * @param mixed $response
   * @return bool
   */
  private function isResponse($response): bool {
    return is_object($response) && $response instanceof Response;
  }

  /**
   * Check if the response is HTML
   *
   * @param Response $response
   * @return bool
   */
  private function isHtml(Response $response): bool {
    $contentType = $response->headers->get('Content-Type');
    return strtolower(strtok($contentType, ';')) == 'text/html';
  }

  /**
   * @param string $html
   * @return string
   */
  private function css(string $html): string {
    preg_match($this->cssRegex, $html, $tags);

    if (empty($tags)) {
      return '';
    }

    $this->dom->loadHTML($tags[1]);

    $linkElements = $this->dom->getElementsByTagName('link');

    $links = collect();

    /**
     * @var DOMElement $link
     */
    foreach ($linkElements as $link) {
      if ($link->getAttribute('rel') == 'stylesheet') {
        $links->add($link->getAttribute('href'));
      }
    }

    // this is the unique name of aggregated src of scripts
    $aggregateFileName = md5($links->implode(','));
    $aggregateFilePath = "{$aggregateFileName}.css";

    if (!$this->disk->exists($aggregateFilePath)) {
      $content = $links->map(function (string $fileName) {
        $fileUrl = public_path($fileName);
        return file_get_contents($fileUrl);
      })
        ->implode('');

      $this->disk->put($aggregateFilePath, $content);
    }

    return $aggregateFilePath;
  }

  /**
   * @param string $html
   * @return string
   */
  private function js(string $html): string {
    preg_match($this->jsRegex, $html, $tags);

    if (empty($tags)) {
      return '';
    }

    $this->dom->loadHTML($tags[1]);

    $scriptElements = $this->dom->getElementsByTagName('script');

    $scripts = collect();

    /**
     * @var DOMElement $script
     */
    foreach ($scriptElements as $script) {
      $scripts->add($script->getAttribute('src'));
    }

    // this is the unique name of aggregated src of scripts
    $aggregateFileName = md5($scripts->implode(','));
    $aggregateFilePath = "{$aggregateFileName}.js";

    if (!$this->disk->exists($aggregateFilePath)) {
      $content = $scripts->map(function (string $fileName) {
        $fileUrl = public_path($fileName);
        return file_get_contents($fileUrl);
      })
        ->implode('');

      $this->disk->put($aggregateFilePath, $content);
    }

    return $aggregateFilePath;
  }
}
