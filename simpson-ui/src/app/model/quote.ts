import {CharacterDirection} from './character-direction';

export class Quote {
  public text: string;
  public character: string;
  public imageUrl: string;
  public characterDirection: CharacterDirection;

  constructor(text: string, character: string, imageUrl: string, characterDirection: CharacterDirection) {
    this.text = text;
    this.character = character;
    this.imageUrl = imageUrl;
    this.characterDirection = characterDirection;
  }
}
