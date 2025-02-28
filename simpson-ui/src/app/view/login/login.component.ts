import {Component, OnInit} from '@angular/core';
import {AuthService} from '../../service/auth.service';
import {FormsModule} from '@angular/forms';
import {Router} from '@angular/router';
import {NgIf} from '@angular/common';

@Component({
  selector: 'app-login',
  imports: [
    FormsModule,
    NgIf
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss'
})
export class LoginComponent implements OnInit {

  error: string | null = null;

  username: string = "";
  password: string = "";

  constructor(private readonly authService: AuthService,
              private readonly router: Router) {
  }

  login(): void {
    this.authService.login(this.username, this.password).subscribe({
      next: () => {
        this.router.navigate(['/quotes']);
      },
      error: (err) => {
        if(err.status === 401) {
          this.error = 'Invalid credentials';
        } else {
          this.error = err.message;
        }
      }
    });
  }

  ngOnInit(): void {
    if (this.authService.getToken() !== null) {
      this.router.navigate(['/quotes']);
    }
  }
}
