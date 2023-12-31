/**
 * @module direction/directionui
 */

import { Plugin } from 'ckeditor5/src/core';
import { ButtonView } from 'ckeditor5/src/ui';

import directionLeftIcon from '../../../../icons/ltr.svg';
import directionRightIcon from '../../../../icons/rtl.svg';

/**
 * The default direction UI plugin.
 *
 * It introduces the 'direction:rtl'` buttons.
 */
export default class DirectionUI extends Plugin {
  /**
   * @inheritDoc
   */
  static get pluginName() {
    return 'DirectionUI';
  }

  /**
   * @inheritDoc
   */
  init() {
    const { editor } = this;
    const { componentFactory } = editor.ui;

    componentFactory.add('direction', (locale) => {
      const buttonView = new ButtonView(locale);
      const command = editor.commands.get('changeDirection');

      // Create the toolbar button.
      buttonView.set({
        label: Drupal.t('Toggle direction'),
        icon:
          locale.contentLanguageDirection === 'rtl'
            ? directionLeftIcon
            : directionRightIcon,
        tooltip: true,
        isToggleable: true,
      });

      // Bind the state of the button to the command.
      buttonView.bind('isEnabled').to(command);
      buttonView.bind('isOn').to(command, 'value');

      // Execute the command when the button is clicked (executed).
      this.listenTo(buttonView, 'execute', () => {
        editor.execute('changeDirection');
        editor.editing.view.focus();
      });

      return buttonView;
    });
  }
}
