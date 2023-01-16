<?php
namespace ShasBgt\SbTmt;

use Illuminate\Support\ServiceProvider;

class SbTmtServiceProvider extends ServiceProvider {

  public function boot()
  {
    $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'sammy_sb');

    $this->mergeConfigFrom(
        __DIR__.'/../config/sammy_sb_tmt.php', 'sammy_sb_tmt'
    );

  }

  public function register()
  {

  }
}
?>
