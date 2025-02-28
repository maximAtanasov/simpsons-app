import { Routes } from '@angular/router';
import {QuotesComponent} from './view/quotes/quotes.component';
import {LoginComponent} from './view/login/login.component';

export const routes: Routes = [
  {
    path: 'quotes',
    component: QuotesComponent
  },
  {
    path: 'login',
    component: LoginComponent
  },
  {
  path: '',
  redirectTo: '/login',
  pathMatch: 'full'
}];
