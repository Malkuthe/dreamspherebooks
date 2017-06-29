<?php
namespace Grav\Theme;

require_once __DIR__ . '/vendor/autoload.php';
use Leafo\ScssPhp\Server;
use Leafo\ScssPhp\Compiler;

use Grav\Plugin\Admin;
use Grav\Common\Theme;
use Grav\Common\Page\Page;
use Grav\Common\Page\Pages;
use Grav\Common\Page\Collection;
use Grav\Common\Uri;
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\File\YamlFile;
use RocketTheme\Toolbox\File\File;

class Dreamspherebooks extends Theme
{
  public static function getSubscribedEvents()
  {
      return [
          'onThemeInitialized' => ['onThemeInitialized',0]
      ];
  }

  public function onThemeInitialized()
  {

    if ($this->isAdmin()) {
      return;
    }

    $this->enable([
        'onPageInitialized' => ['onPageInitialized', 0]
    ]);
  }

  public function onPageInitialized(Event $e)
  {
    $config = YamlFile::instance('user://config/themes/dreamspherebooks.yaml');
    $checkfile = YamlFile::instance(__DIR__ . '/customization/checkfile.yaml');
    if ($config->exists()) {
      if ($checkfile->exists()) {
        $isChanged = $this->isChanged($config, $checkfile);
        if ($isChanged) {
          $menu_enabled = $this->grav['config']->get('system.theme');
          $this->grav['log']->notice($menu_enabled);
          $this->updateCustomization(TRUE,'aerendur');
          $this->saveCheckFile($config,$checkfile);
        }
      } else {
        $this->grav['log']->warning('Customization Checkfile does not exist');
        $this->saveCheckFile($config, $checkfile);
      }
    }
  }

  public function onCollectionProcessed(Event $e) {
    $collection = $e['collection'];
    $params = $collection->params();
    if ($params['name'] == 'featured') {
      $pages = $this->grav['pages']->instances();
      $page = $this->grav['page'];
      foreach ($pages as $p) {
        $header = $p->header();
        if (isset($header->featured['enabled'])) {
          if ($header->featured['enabled']) {
            $collection->addPage($p);
          }
        }
      }
      $collection->order('date','desc');
    }
  }

  //
  public function isChanged( YamlFile $file, YamlFile $checkfile ) {
    $check = $checkfile->content();
    $checksum = $check['checksum']; // md5 hash of last change to $file
    $checkmdate = $check['mdate']; // last modified date of $file
    $filehash = md5_file($file->filename()); // current md5 hash of $file
    $filemdate = $file->modified(); // current modified date of $file

    if ( $checkmdate != $filemdate ) {
      if ( $checksum == $filehash ) {
        return false;
      }
      return true;
    } elseif ( $checksum != $filehash ) {
      return true;
    } else {
      return false;
    }
  }

  public function saveCheckFile( YamlFile $file, YamlFile $checkfile ) {
    $check = $checkfile->content();
    $checksum = md5_file($file->filename());
    $mdate = $file->modified();
    $check['checksum'] = $checksum;
    $check['mdate'] = $mdate;
    $checkfile->save($check);
  }

  public function updateCustomization( $ischild, $parent ) {
    $dir = __DIR__;
    $twig = $this->grav['twig'];
    $raw = $twig->processTemplate('css/customization.css.twig');
    $cust = File::instance(__DIR__ . '/scss/template/_customization.scss');
    if (!$cust->exists()) {
      $cust->save();
    }
    $template = File::instance(__DIR__ . '/scss/template.scss');
    $scss = new Compiler();
    $scss->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
    if ($ischild) {
      $par = $this->getParentPath( $parent,$dir );
    }
    $scss->setImportPaths([
      $dir . '/scss',
      $par . '/scss',
      $par . '/bower_components/foundation-sites/scss',
      $par . '/bower_components/motion-ui/scss'
    ]);
    $compiled = $scss->compile( $template->raw() );
    $css = File::instance(__DIR__ . '/css/template.css');
    if (!$css->exists()) {
      $css->save();
    }
    $cssraw = $css->raw();
    $cssraw = $compiled;
    $css->save($cssraw);
  }

  public function getParentPath( $parent, $childdir ) {
    $env = $this->grav['uri']->environment();
    if (!isset($parent)) {
      throw new Exception('No Parent Name Provided');
    } else {
      if ($env != 'dreamspherebooks.dev') {
        $dirArray = explode('/',$childdir);
        $dirArray[count($dirArray)-1] = $parent;
        $par = implode('/',$dirArray);

        return $par;
      } else {
        $dirArray = explode('\\',$childdir);
        $dirArray[count($dirArray)-1] = $parent;
        $par = implode('\\',$dirArray);

        return $par;
      }
    }
  }
}
