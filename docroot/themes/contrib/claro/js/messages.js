/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

Drupal.theme.message = function (_ref, _ref2) {
  var text = _ref.text;
  var type = _ref2.type,
      id = _ref2.id;

  var messagesTypes = Drupal.Message.getMessageTypeLabels();
  var messageWrapper = document.createElement('div');

  messageWrapper.setAttribute('class', 'messages messages--' + type);
  messageWrapper.setAttribute('role', type === 'error' || type === 'warning' ? 'alert' : 'status');
  messageWrapper.setAttribute('aria-labelledby', id + '-title');
  messageWrapper.setAttribute('data-drupal-message-id', id);
  messageWrapper.setAttribute('data-drupal-message-type', type);

  messageWrapper.innerHTML = '\n    <div class="messages__header">\n      <h2 id="' + id + '-title" class="messages__title">\n        ' + messagesTypes[type] + '\n      </h2>\n    </div>\n    <div class="messages__content">\n      ' + text + '\n    </div>\n  ';

  return messageWrapper;
};