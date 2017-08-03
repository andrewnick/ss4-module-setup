import Injector from 'lib/Injector';
import CharacterCounter from '../components/CharacterCounter';

document.addEventListener('DOMContentLoaded', () => {
  Injector.transform('character-count-transform', (updater) => {
    updater.component('TextField', CharacterCounter, 'CharacterCounter');
  });
});