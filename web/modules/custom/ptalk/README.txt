README.txt
____________________


DESCRIPTION
____________________

A Private Conversation module is an internal threaded messaging system which allows send and recieving private messages. Module allows start private conversation between one and multiple participant. This module is similar to the Private Message module for Drupal 7 and based on it. The module worked on api of the Drupal 8 core, which make it safety and flexible for using and extending of the functionality.


SUB-MODULES
____________________

The api of the Drupal Core and module Private Conversation allows extend the standard functionality of the module. The below is represented some of the sub-modules which you may enable additionaly and extend the standart functionality of the module Private Conversation.

* Block User Messages (ptalk_block_user): Allows users to block other users from sending them messages.


PERMISSIONS
____________________

The Private Conversation module and its sub-modules define a variety of permissions. These include permissions for reading conversations, starting conversations, deleting conversations and messages, and more. All of these permissions can be found at admin/people/permissions.


INSTALLATION
____________________

To install Private Conversation, do the following:

1. Extract the tar ball that you downloaded from Drupal.org.

2. Upload the ptalk directory and all its contents to your modules directory.

3. Visit admin/modules and enable the Private Conversation module and any of its sub-modules.  All of these modules can be found within the "Mail" fieldset.

Also you may instal the specific version of the Private Conversation module via Composer.


CONFIGURATION
____________________

To configure this module do the following:

1. Go to People -> Permissions (admin/people/permissions) and find the relevant module permissions underneath the Private Conversation permission. If you are not logged in as user #1, you must give at least one role (probably the administrator role) the 'Administer private conversation module and settings' permission to configure this module. 

2. On this same Permissions page, give at least one role the 'Read private conversation' permission and the 'Start new private conversation' permission. This will allow participants of that role to read and start private conversations.

3. Go to Configuration -> Private Conversation (admin/config/people/ptalk) and configure the module settings per your requirements. If you have various sub-modules enabled, their settings pages may appear as tabs on this page. 

4. Login as a user with the role we specified in Step #2. Follow the link 'Private conversations' of the menu 'User account menu' and start using the module.


API
____________________

The module has a powerful API, which you can see in the file ptalk.api.php of the module directory.

