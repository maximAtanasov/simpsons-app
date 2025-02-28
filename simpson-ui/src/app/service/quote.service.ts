import { Injectable } from '@angular/core';
import {HttpClient, HttpErrorResponse} from '@angular/common/http';
import {catchError, Observable, throwError} from 'rxjs';
import {Quote} from '../model/quote';

@Injectable({
  providedIn: 'root'
})
export class QuoteService {

  private readonly apiUrl = "http://localhost:8080/api/quotes";

  constructor(private readonly http: HttpClient) { }

  getData(): Observable<Quote[]> {
    return this.http.get<Quote[]>(this.apiUrl)
      .pipe(catchError(this.handleError.bind(this)));
  }

  private handleError(error: HttpErrorResponse): Observable<never> {
    return throwError(() => new Error(error.message || 'Server Error'));
  }
}
