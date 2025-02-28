import {Quote} from './quote';
import {CharacterDirection} from './character-direction';

describe('Quote', () => {
  it('should create an instance', () => {
    expect(new Quote('test', 'Homer', 'url', CharacterDirection.RIGHT)).toBeTruthy();
  });
});
