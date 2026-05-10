import { Component } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../services/auth.service';

@Component({
  selector: 'app-login',
  imports: [RouterLink, FormsModule, CommonModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  email    = '';
  password = '';
  nombre   = '';   // solo para registro

  cargando   = false;
  error      = '';
  modoRegistro = false;

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  onSubmit(): void {
    this.error = '';

    if (this.modoRegistro) {
      this.registrar();
    } else {
      this.iniciarSesion();
    }
  }

  private iniciarSesion(): void {
    if (!this.email || !this.password) {
      this.error = 'Por favor completa todos los campos';
      return;
    }

    this.cargando = true;

    this.authService.login(this.email, this.password).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success && res.user) {
          // Redirigir según el rol
          if (res.user.isAdmin) {
            this.router.navigate(['/admin']);
          } else {
            this.router.navigate(['/']);
          }
        }
      },
      error: (err) => {
        this.cargando = false;
        // El backend devuelve el mensaje de error en el body
        this.error = err.error?.message || 'Error al conectar con el servidor';
      }
    });
  }

  private registrar(): void {
    if (!this.nombre || !this.email || !this.password) {
      this.error = 'Por favor completa todos los campos';
      return;
    }

    this.cargando = true;

    this.authService.register(this.nombre, this.email, this.password).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success) {
          this.router.navigate(['/']);
        }
      },
      error: (err) => {
        this.cargando = false;
        this.error = err.error?.message || 'Error al registrar la cuenta';
      }
    });
  }

  toggleModo(): void {
    this.modoRegistro = !this.modoRegistro;
    this.error = '';
    this.email = '';
    this.password = '';
    this.nombre = '';
  }
}
