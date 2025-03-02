import {Component, OnInit} from '@angular/core';
import {QuoteService} from '../../service/quote.service';
import {Quote} from '../../model/quote';
import {NgForOf, NgIf} from '@angular/common';
import {CharacterDirection} from '../../model/character-direction';
import {HttpErrorResponse} from '@angular/common/http';

@Component({
  selector: 'app-quotes',
  imports: [
    NgForOf,
    NgIf
  ],
  templateUrl: './quotes.component.html',
  styleUrl: './quotes.component.scss'
})
export class QuotesComponent implements OnInit {

  quotes: Quote[] = [];
  showSpinner: boolean = false;

  error: string | null = null;
  protected readonly CharacterDirection = CharacterDirection;

  constructor(private readonly quoteService: QuoteService) {
  }

  ngOnInit(): void {
    this.fetchQuotes();
  }

  fetchQuotes(): void {
    this.error = null;
    this.showSpinner = true;
    if (this.quotes.length === 5) {
      this.quotes.pop();
    }
    this.quoteService.getData().subscribe({
      next: (result: any[]) => {
        this.quotes = result;
        this.showSpinner = false;
      },
      error: (err: HttpErrorResponse) => {
        if (err.message.includes('503')) {
          this.error = 'The Simpsons Quote API is unavailable.';
        } else {
          this.error = err.message;
        }
        this.showSpinner = false;
      }
    });
  }
}
