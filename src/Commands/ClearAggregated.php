<?php


namespace Gioppy\StatamicAggregateAssets\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Statamic\Console\RunsInPlease;

class ClearAggregated extends Command {

  use RunsInPlease;

  protected $signature = 'statamic:aggregated:clear';

  protected $description = 'Clear all generated aggregated files.';

  public function handle() {
    $disk = Storage::disk('aggregated');

    collect($disk->allFiles())
      ->each(function (string $file) use ($disk) {
        $disk->delete($file);
      });

    $this->info('Aggregated files cleared!');
  }
}
