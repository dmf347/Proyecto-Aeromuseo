import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-verify-email',
  imports: [CommonModule, RouterLink],
  templateUrl: './verify-email.html',
  standalone: true
})
export class VerifyEmail implements OnInit {
  cargando = true;
  success = false;
  message = 'Verificando tu cuenta...';
  nombre = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private authService: AuthService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    const token = this.route.snapshot.queryParamMap.get('token');

    if (!token) {
      this.cargando = false;
      this.success = false;
      this.message = 'No se proporcionó ningún token de verificación.';
      return;
    }

    this.authService.verifyEmail(token).subscribe({
      next: (res) => {
        this.cargando = false;
        this.success = res.success;
        this.message = res.message;
        if (res.nombre) {
          this.nombre = res.nombre;
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        this.success = false;
        this.message = err.error?.message || 'Error al verificar la cuenta. El enlace puede haber expirado.';
        this.cdr.detectChanges();
      }
    });
  }
}
