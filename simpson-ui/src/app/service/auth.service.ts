import {Injectable} from '@angular/core';
import {tap} from 'rxjs';
import {HttpClient} from '@angular/common/http';
import {AppComponent} from '../app.component';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private readonly TOKEN_KEY = 'accessToken';

  constructor(private readonly http: HttpClient) {
  }

  login(username: string, password: string) {
    return this.http.post<Object>(`${AppComponent.API_URL}/login`, {username, password})
      .pipe(tap((value: any) => this.setToken(value.token)));
  }

  public setToken(token: string): void {
    sessionStorage.setItem(this.TOKEN_KEY, token);
  }

  public getToken(): string | null {
    return sessionStorage.getItem(this.TOKEN_KEY);
  }
}
