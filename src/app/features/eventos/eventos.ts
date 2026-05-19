import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';
import { AdminService, EventoAdmin } from '../../services/admin.service';

@Component({
  selector: 'app-eventos',
  imports: [RouterLink, CommonModule],
  templateUrl: './eventos.html',
  styleUrl: './eventos.css',
})
export class Eventos implements OnInit {
  eventos: EventoAdmin[] = [];
  cargando = true;
  error = false;

  constructor(
    public authService: AuthService,
    private adminService: AdminService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.cargarEventos();
  }

  cargarEventos(): void {
    this.cargando = true;
    this.error = false;
    console.log('[Eventos] Iniciando carga de eventos...');

    this.adminService.getEventos().subscribe({
      next: (data) => {
        console.log('[Eventos] Datos recibidos:', data);
        // Mostrar todos salvo los explícitamente desactivados (activo === 0)
        this.eventos = data.filter(e => e.activo !== 0);
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('[Eventos] Error al cargar eventos:', err);
        this.error = true;
        this.cargando = false;
        this.cdr.detectChanges();
      }
    });
  }

  formatearFecha(fecha: string): string {
    const date = new Date(fecha + 'T00:00:00');
    return date.toLocaleDateString('es-ES', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }
}
