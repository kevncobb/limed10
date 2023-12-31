<?php

namespace Drupal\aggregator;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining an aggregator feed entity.
 */
interface FeedInterface extends ContentEntityInterface {

  /**
   * Sets the title of the feed.
   *
   * @param string $title
   *   The short title of the feed.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setTitle($title);

  /**
   * Returns the url to the feed.
   *
   * @return string
   *   The url to the feed.
   */
  public function getUrl();

  /**
   * Sets the url to the feed.
   *
   * @param string $url
   *   A string containing the url of the feed.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setUrl($url);

  /**
   * Returns the refresh rate of the feed in seconds.
   *
   * @return int
   *   The refresh rate of the feed in seconds.
   */
  public function getRefreshRate();

  /**
   * Sets the refresh rate of the feed in seconds.
   *
   * @param int $refresh
   *   The refresh rate of the feed in seconds.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setRefreshRate($refresh);

  /**
   * Returns the last time where the feed was checked for new items.
   *
   * @return int
   *   The timestamp when new items were last checked for.
   */
  public function getLastCheckedTime();

  /**
   * Sets the time when this feed was queued for refresh, 0 if not queued.
   *
   * @param int $checked
   *   The timestamp of the last refresh.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setLastCheckedTime($checked);

  /**
   * Returns the time when this feed was queued for refresh, 0 if not queued.
   *
   * @return int
   *   The timestamp of the last refresh.
   */
  public function getQueuedTime();

  /**
   * Sets the time when this feed was queued for refresh, 0 if not queued.
   *
   * @param int $queued
   *   The timestamp of the last refresh.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setQueuedTime($queued);

  /**
   * Returns the parent website of the feed.
   *
   * @return string
   *   The parent website of the feed.
   */
  public function getWebsiteUrl();

  /**
   * Sets the parent website of the feed.
   *
   * @param string $link
   *   A string containing the parent website of the feed.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setWebsiteUrl($link);

  /**
   * Returns the description of the feed.
   *
   * @return string
   *   The description of the feed.
   */
  public function getDescription();

  /**
   * Sets the description of the feed.
   *
   * @param string $description
   *   The description of the feed.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setDescription($description);

  /**
   * Returns the primary image attached to the feed.
   *
   * @return string
   *   The URL of the primary image attached to the feed.
   */
  public function getImage();

  /**
   * Sets the primary image attached to the feed.
   *
   * @param string $image
   *   An image URL.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setImage($image);

  /**
   * Returns the calculated hash of the feed data, used for validating cache.
   *
   * @return string
   *   The calculated hash of the feed data.
   *
   * @deprecated in aggregator:2.1.0 and is removed from aggregator:3.0.0.  Use
   *   \Drupal::service('aggregator.items.importer')->getHash($feed); instead.
   *
   * @see https://www.drupal.org/node/3386907
   */
  public function getHash();

  /**
   * Sets the calculated hash of the feed data, used for validating cache.
   *
   * @param string $hash
   *   A string containing the calculated hash of the feed. Must contain
   *   US ASCII characters only.
   *
   * @return $this
   *   The class instance that this method is called on.
   *
   * @deprecated in aggregator:2.1.0 and is removed from aggregator:3.0.0.  Use
   *   \Drupal::service('aggregator.items.importer')->setHash($feed, $hash);
   *   instead.
   *
   * @see https://www.drupal.org/node/3386907
   */
  public function setHash($hash);

  /**
   * Returns the entity tag HTTP response header, used for validating cache.
   *
   * @return string
   *   The entity tag HTTP response header.
   */
  public function getEtag();

  /**
   * Sets the entity tag HTTP response header, used for validating cache.
   *
   * @param string $etag
   *   A string containing the entity tag HTTP response header.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setEtag($etag);

  /**
   * Return when the feed was modified last time.
   *
   * @return int
   *   The timestamp of the last time the feed was modified.
   */
  public function getLastModified();

  /**
   * Sets the last modification of the feed.
   *
   * @param int $modified
   *   The timestamp when the feed was modified.
   *
   * @return $this
   *   The class instance that this method is called on.
   */
  public function setLastModified($modified);

  /**
   * Deletes all items from a feed.
   *
   * This will also reset the last checked and modified time of the feed and
   * save it.
   *
   * @return $this
   *   The class instance that this method is called on.
   *
   * @see \Drupal\aggregator\ItemsImporterInterface::delete()
   */
  public function deleteItems();

  /**
   * Updates the feed items by triggering the import process.
   *
   * This will also update the last checked time of the feed and save it.
   *
   * @return bool
   *   TRUE if there is new content for the feed FALSE otherwise.
   *
   * @see \Drupal\aggregator\ItemsImporterInterface::refresh()
   */
  public function refreshItems();

}
