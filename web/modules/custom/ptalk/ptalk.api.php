<?php

/**
 * @file
 * Hooks provided by the Private Conversation module.
 */

use Drupal\ptalk\MessageInterface;
use Drupal\ptalk\ThreadInterface;
use Drupal\Core\Url;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Act after status of the messages was changed.
 *
 * @param $mids
 *   The ids of the ptalk_message entities status which of was changed.
 * @param $status
 *   Either PTALK_READ or PTALK_UNREAD
 * @param \Drupal\Core\Session\AccountInterface $account
 *   The account object, defaults to the current user
 *
 * @see ptalk_message_change_status()
 */
function hook_ptalk_message_status_changed($mids, $status, $account) {
  \Drupal::logger('example')->info('The user @user read the messages with next ids - @ids.', ['@user' => $account->getUserName(), '@ids' => implode($mids)]);
}

/**
 * Act after status 'delete' of the ptalk_message entity was changed.
 *
 * @param \Drupal\ptalk\MessageInterface $ptalk_message
 *   The ptalk_message entity.
 * @param $delete
 *   Either deletes or restores the message (PTALK_DELETED => delete, PTALK_UNDELETED => restore)
 * @param \Drupal\Core\Session\AccountInterface $account
 *   The account object for which the delete action should be carried out.
 *   Set to NULL to delete for all users.
 *
 * @see ptalk_message_change_delete()
 */
function hook_ptalk_message_delete_changed($message, $delete, $account) {
  \Drupal::logger('example')->info('The user @user @action the message @message.', ['@user' => $account->getUserName(), '@message' => $message->id(), '@action' => $delete == PTALK_UNDELETED ? 'restore' : 'delete']);
}

/**
 * Act after indexed information of the specific recipient of the message was deleted.
 *
 * @param int $mid
 *   The ID of the ptalk_message entity.
 * @param int $account_id
 *   Account ID for which index must be deleted,
 *   if NULL then deletion will be done for all recipients.
 *
 * @see ptalk_delete_message_index()
 */
function hook_ptalk_delete_message_index($mid, $account_id) {
  // Delete the data related to the message index.
  $db_delete = db_delete('example')
    ->condition('example_id', $mid);

  if ($account_id) {
    $db_delete->condition('account_id', $account_id);
  }

  $db_delete->execute();
}

/**
 * Act after indexed information of the specific participant of the thread was deleted.
 *
 * @param int $tid
 *   The ID of the ptalk_thread entity.
 * @param int $account_id
 *   Account ID for which index must be deleted,
 *   if NULL then deletion will be done for all participants.
 *
 * @see ptalk_delete_thread_index()
 */
function hook_ptalk_delete_thread_index($tid, $account_id) {
  // Delete the data related to the thread index.
  $db_delete = db_delete('example')
    ->condition('example_id', $tid);

  if ($account_id) {
    $db_delete->condition('account_id', $account_id);
  }

  $db_delete->execute();
}

/**
 * Act after thread messages was marked as (un)read.
 *
 * @param \Drupal\ptalk\ThreadInterface $ptalk_thread
 *   The ptalk_thread entity status of which was changed.
 * @param $changed
 *   The ids of the ptalk_message entities, status which of was changed.
 * @param $status
 *   Either PTALK_READ or PTALK_UNREAD, sets the new status.
 * @param \Drupal\Core\Session\AccountInterface $account
 *   The account object, defaults to the current user.
 *
 * @see ptalk_thread_change_status()
 */
function hook_ptalk_thread_status_changed($thread, $changed, $status, $account) {
  \Drupal::logger('example')->info('The user @user marked the next messages - @messages - as @action.', ['@user' => $account->getUserName(), '@messages' => implode($changed), '@action' => $status == PTALK_UNREAD ? 'unread' : 'read']);
}

/**
 * Act after thread messages was marked as (un)delete.
 *
 * @param \Drupal\ptalk\ThreadInterface $ptalk_thread
 *   The ptalk_thread entity.
 * @param $changed
 *   The ids of the ptalk_message entities, status which of was changed.
 * @param $delete
 *   Indicates if the thread should be deleted or restored. PTALK_DELETED => delete, PTALK_UNDELETED => restore.
 * @param \Drupal\Core\Session\AccountInterface $account
 *   The account object for which the delete action should be carried out.
 *
 * @see ptalk_thread_change_delete()
 */
function hook_ptalk_thread_delete_changed($thread, $changed, $delete, $account) {
  \Drupal::logger('example')->info('The user @user @action the next messages - @messages', ['@user' => $account->getUserName(), '@messages' => implode($changed), '@action' => $delete == PTALK_UNDELETED ? 'undelete' : 'delete']);
}

/**
 * Query to attache additional information about recipient of the message.
 *
 * This information will be attached for all recipients of the message,
 * before message will be saved and will be available
 * on stage hook_ENTITY_TYPE(ptalk_message)_save()
 * and hook_ENTITY_TYPE(ptalk_message)_insert().
 *
 * @param $query
 *   Query object
 *
 * @see ptalk_load_message_recipients()
 */
function hook_query_ptalk_load_message_recipients_alter($query) {
  // See a live example in modules ptalk and sub-module block_user (
  // ptalk_load_message_recipients() and ptalk_block_user_query_ptalk_load_message_recipients_alter()).
}

/**
 * Allows to block author from sending a message.
 *
 * The author may be blocked from sending a message to all recipients or to a 
 * specific recipient. To attach the information, based on which must be done
 * blocking you may use hook_query_ptalk_load_message_recipients_alter() - if that
 * information is stored in database.
 *
 * @param object $author
 *   Author of the message which must be blocked,
 *   gererated by the function ptalk_load_message_recipients().
 * @param array $recipients
 *   An array with recipients objects of the message
 *   generated by the function ptalk_load_message_recipients().
 * @param $context
 *   Additional information. Can contain the thread object (if it's a reply to the thread).
 *
 * @return
 *   An array with key - type of the blocking (possible values is 'author' and 'recipients')
 *   and sub-key - name of the blocking reason (must be unique in all implementations).
 *   This array contain an array of all recipients blocked with specific reason and keyed by key 'ids',
 *   and an array keyed with key 'message' with type of the message and prlural/singular version of the message.
 */
function hook_ptalk_block_message($author, $recipients, $context) {
  // See a live example in modules ptalk and sub-module block_user (
  // ptalk_ptalk_block_message() and ptalk_block_user_ptalk_block_message()).
}

/**
 * Alter the links of a message.
 *
 * @param array &$links
 *   A renderable array representing the message links.
 * @param \Drupal\ptalk\MessageInterface $entity
 *   The message being rendered.
 * @param array &$context
 *   The view mode in which the message is being viewed.
 *
 * @see \Drupal\ptalk\MessageViewBuilder::renderLinks()
 * @see \Drupal\ptalk\MessageViewBuilder::buildLinks()
 */
function hook_ptalk_message_links_alter(array &$links, MessageInterface $entity, array &$context) {
  $links['mymodule'] = [
    '#theme' => 'links__ptalk_message__mymodule',
    '#attributes' => ['class' => ['links', 'inline']],
    '#links' => [
      'message-report' => [
        'title' => t('Report'),
        'url' => Url::fromRoute('message_test.report', ['message' => $entity->id()], ['query' => ['token' => \Drupal::getContainer()->get('csrf_token')->get("private/message/{$entity->id()}/report")]]),
      ],
    ],
  ];
}

/**
 * Alter the links of a thread.
 *
 * @param array &$links
 *   A renderable array representing the thread links.
 * @param \Drupal\ptalk\ThreadInterface $entity
 *   The thread being rendered.
 * @param array &$context
 *   The view mode in which the thread is being viewed.
 *
 * @see \Drupal\ptalk\ThreadViewBuilder::renderLinks()
 * @see \Drupal\ptalk\ThreadViewBuilder::buildLinks()
 */
function hook_ptalk_thread_links_alter(array &$links, ThreadInterface $entity, array &$context) {
  $links['mymodule'] = [
    '#theme' => 'links__ptalk_thread__mymodule',
    '#attributes' => ['class' => ['links', 'inline']],
    '#links' => [
      'thread-report' => [
        'title' => t('Report'),
        'url' => Url::fromRoute('thread_test.report', ['thread' => $entity->id()], ['query' => ['token' => \Drupal::getContainer()->get('csrf_token')->get("private/conversation/{$entity->id()}/report")]]),
      ],
    ],
  ];
}

/**
 * @} End of "addtogroup hooks".
 */
