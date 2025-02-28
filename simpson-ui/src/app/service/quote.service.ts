import {Injectable} from '@angular/core';
import {HttpClient, HttpErrorResponse} from '@angular/common/http';
import {catchError, Observable, throwError} from 'rxjs';
import {Quote} from '../model/quote';
import {AppComponent} from '../app.component';
import {AuthService} from './auth.service';
import {Router} from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class QuoteService {

  constructor(private readonly http: HttpClient,
              private readonly authService: AuthService,
              private readonly router: Router) {
  }

  getData(): Observable<Quote[]> {
    return this.http.get<Quote[]>(`${AppComponent.API_URL}/quotes`, {
      headers: {
        'Authorization': `Bearer ${this.authService.getToken()}`,
        'Content-Type': 'application/json'
      }
    }, ).pipe(catchError(this.handleError.bind(this)));
  }

  private handleError(error: HttpErrorResponse): Observable<never> {
    if(error.status === 401) {
      this.router.navigate(['/login']);
    }
    return throwError(() => new Error(error.message));
  }
}
