import { Component, ChangeDetectorRef } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  imports: [RouterLink, FormsModule, CommonModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  email = '';
  password = '';
  nombre = '';   // solo para registro

  cargando = false;
  error = '';
  modoRegistro = false;
  modoRecuperar = false;

  successMessage = '';

  constructor(
    private authService: AuthService,
    private router: Router,
    private cdr: ChangeDetectorRef
  ) { }

  onSubmit(): void {
    this.error = '';
    this.successMessage = '';

    if (this.modoRegistro) {
      this.registrar();
    } else if (this.modoRecuperar) {
      this.recuperarContrasena();
    } else {
      this.iniciarSesion();
    }
  }

  private recuperarContrasena(): void {
    if (!this.email) {
      this.error = 'Por favor, introduce tu correo electrónico';
      return;
    }

    this.cargando = true;
    this.authService.forgotPassword(this.email).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success) {
          this.successMessage = res.message;
          // Dejar la pantalla en modo login, o dejar en recuperar y limpiar
          this.modoRecuperar = false;
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        this.error = err.error?.message || 'Error al procesar la solicitud';
        this.cdr.detectChanges();
      }
    });
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
        this.cdr.detectChanges();
        if (res.success && res.user) {
          // Redirigir según el rol
          if (res.user.rol === 'admin') {
            this.router.navigate(['/admin']);
          } else {
            this.router.navigate(['/']); // Redirigir al inicio
          }
        }
      },
      error: (err) => {
        this.cargando = false;
        // El backend devuelve el mensaje de error en el body
        this.error = err.error?.message || 'Error al conectar con el servidor';
        this.cdr.detectChanges();
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
          this.successMessage = res.message || 'Cuenta creada correctamente. Revisa tu bandeja de entrada para verificar el email.';
          this.modoRegistro = false;
          this.password = '';
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        this.error = err.error?.message || 'Error al registrar la cuenta';
        this.cdr.detectChanges();
      }
    });
  }

  toggleModo(): void {
    this.modoRegistro = !this.modoRegistro;
    this.modoRecuperar = false;
    this.error = '';
    this.successMessage = '';
    this.email = '';
    this.password = '';
    this.nombre = '';
  }

  toggleRecuperar(): void {
    this.modoRecuperar = true;
    this.modoRegistro = false;
    this.error = '';
    this.successMessage = '';
  }
}
