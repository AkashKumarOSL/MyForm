<?php
namespace Drupal\Task1;

use Drupal\node\Entity\Node;

class addImportContent {
  public static function addImportContentItem($item, &$context){
    $context['sandbox']['current_item'] = $item;
    $message = 'Creating ' . $item['title'];
    $results = array();
    create_node($item);
    $context['message'] = $message;
    $context['results'][] = $item;
  }
  function addImportContentItemCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One node created successfully.', '@count nodes created successfully.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }
}


function create_node($item) {
  $node_data['type'] = 'article';
  $node_data['title'] = $item['title'];
  $node_data['body']['value'] = $item['content'];
  $node_data['field_unique_id']['value'] = $item['id'];
  $node_data['field_unique_id']['value'] = $item['id'];
  $node = Node::create($node_data);
  $node->setPublished(TRUE);
  $node->save();
} 