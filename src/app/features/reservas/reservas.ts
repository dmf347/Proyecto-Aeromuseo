import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { ReservasService } from '../../services/reservas.service';

@Component({
  selector: 'app-reservas',
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './reservas.html',
  standalone: true
})
export class Reservas implements OnInit {
  fecha_visita: string = '';
  num_personas: number = 1;
  comentarios: string = '';

  cargando = false;
  error = '';
  successMessage = '';

  // Get current date string for min date in calendar
  hoy: string = new Date().toISOString().split('T')[0];

  constructor(
    public authService: AuthService,
    private router: Router,
    private reservasService: ReservasService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    // Si no está logueado o es admin, quizá quieras redirigir. 
    // De momento lo gestiona el authGuard, pero aseguramos.
    this.authService.usuario$.subscribe(user => {
      if (!user) {
        this.router.navigate(['/login']);
      }
    });
  }

  onSubmit(): void {
    if (!this.fecha_visita) {
      this.error = 'Debes seleccionar una fecha para la visita.';
      return;
    }
    if (this.num_personas < 1) {
      this.error = 'El número mínimo de personas es 1.';
      return;
    }

    this.error = '';
    this.successMessage = '';
    this.cargando = true;

    const usuario = this.authService.getUsuarioActual();
    if (!usuario) {
      this.error = 'Debes iniciar sesión para reservar.';
      this.cargando = false;
      return;
    }

    this.reservasService.crearReserva({
      usuario_id: usuario.id,
      fecha_visita: this.fecha_visita,
      num_personas: this.num_personas,
      comentarios: this.comentarios
    }).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success) {
          this.successMessage = res.message;
          this.fecha_visita = '';
          this.num_personas = 1;
          this.comentarios = '';
        } else {
          this.error = res.message || 'Error al crear la reserva';
        }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        this.error = 'Ocurrió un error al contactar con el servidor.';
        this.cdr.detectChanges();
        console.error(err);
      }
    });
  }
}
