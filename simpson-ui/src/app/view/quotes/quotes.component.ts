import {Component, OnInit} from '@angular/core';
import {QuoteService} from '../../service/quote.service';
import {Quote} from '../../model/quote';
import {NgClass, NgForOf, NgIf} from '@angular/common';
import {CharacterDirection} from '../../model/character-direction';

@Component({
  selector: 'app-quotes',
  imports: [
    NgForOf,
    NgIf,
    NgClass
  ],
  templateUrl: './quotes.component.html',
  styleUrl: './quotes.component.scss'
})
export class QuotesComponent implements OnInit {

  quotes: Quote[] = [];
  showSpinner: boolean = false;

  error: string | null = "null";

  constructor(private readonly quoteService: QuoteService) {
  }

  ngOnInit(): void {
    this.fetchQuotes();
  }

  fetchQuotes(): void {
    this.error = null;
    this.showSpinner = true;
    this.quotes.pop();
    this.quoteService.getData().subscribe({
      next: (result: any[]) => {
        console.log(result);
        this.quotes = result;
        this.showSpinner = false;
      },
      error: (err) => {
        console.error(err);
        this.error = err.message;
      }
    });
  }
  protected readonly CharacterDirection = CharacterDirection;
}
