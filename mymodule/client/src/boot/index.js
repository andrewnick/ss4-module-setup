import Injector from 'lib/Injector';
import CharacterCounter from '../components/CharacterCounter';
import ElementLayoutField from 'components/ElementLayoutField/ElementLayoutField';


document.addEventListener('DOMContentLoaded', () => {

  Injector.component.register('ElementLayoutField', ElementLayoutField);

  Injector.transform('character-count-transform', (updater) => {
    updater.component('TextField', CharacterCounter, 'CharacterCounter');
  });
});