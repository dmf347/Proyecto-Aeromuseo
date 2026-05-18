import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-reset-password',
  imports: [FormsModule, RouterLink],
  templateUrl: './reset-password.html',
  standalone: true,
})
export class ResetPassword implements OnInit {
  token = '';
  password = '';
  confirmPassword = '';

  cargando = false;
  error = '';
  successMessage = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private authService: AuthService,
    private cdr: ChangeDetectorRef,
  ) {}

  ngOnInit(): void {
    this.token = this.route.snapshot.queryParamMap.get('token') || '';
    if (!this.token) {
      this.error = 'Enlace de recuperación inválido o expirado.';
    }
  }

  onSubmit(): void {
    if (!this.token) return;
    if (!this.password || !this.confirmPassword) {
      this.error = 'Por favor, completa todos los campos.';
      return;
    }
    if (this.password.length < 6) {
      this.error = 'La contraseña debe tener al menos 6 caracteres.';
      return;
    }
    if (this.password !== this.confirmPassword) {
      this.error = 'Las contraseñas no coinciden.';
      return;
    }

    this.error = '';
    this.successMessage = '';
    this.cargando = true;

    this.authService.resetPassword(this.token, this.password).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success) {
          this.successMessage = res.message;
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        this.error = err.error?.message || 'Error al restablecer la contraseña.';
        this.cdr.detectChanges();
      },
    });
  }
}
