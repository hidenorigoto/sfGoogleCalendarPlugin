<?php

/**
 * sfGoogleCalendarPlugin configuration.
 *
 * @package     sfGoogleCalendarPlugin
 * @subpackage  config
 * @author      hidenorigoto
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class sfGoogleCalendarPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
      sfToolkit::addIncludePath(dirname(__FILE__).'/../lib/vendor/ZendGdata/library/');
  }
}
