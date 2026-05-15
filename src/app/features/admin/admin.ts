import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AdminService, ReservaAdmin, EventoAdmin } from '../../services/admin.service';
import { AuthService } from '../../services/auth.service';
import { Router, RouterLink } from '@angular/router';

@Component({
  selector: 'app-admin',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './admin.html'
})
export class Admin implements OnInit {
  activeTab: 'reservas' | 'eventos' = 'reservas';
  
  reservas: ReservaAdmin[] = [];
  eventos: EventoAdmin[] = [];

  // Form for Eventos
  showEventForm = false;
  currentEvent: EventoAdmin = { titulo: '', descripcion: '', fecha: '', hora: '', lugar: '' };

  constructor(
    private adminService: AdminService,
    public authService: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadReservas();
    this.loadEventos();
  }

  loadReservas() {
    this.adminService.getReservas().subscribe(res => this.reservas = res);
  }

  loadEventos() {
    this.adminService.getEventos().subscribe(res => this.eventos = res);
  }

  updateReserva(id: number, estado: 'aprobada' | 'rechazada') {
    this.adminService.updateReservaStatus(id, estado).subscribe(res => {
      if(res.success) this.loadReservas();
    });
  }

  openEventForm(evento?: EventoAdmin) {
    if (evento) {
      this.currentEvent = { ...evento };
    } else {
      this.currentEvent = { titulo: '', descripcion: '', fecha: '', hora: '', lugar: '' };
    }
    this.showEventForm = true;
  }

  saveEvent() {
    if (this.currentEvent.id) {
      this.adminService.updateEvento(this.currentEvent).subscribe(res => {
        if(res.success) {
          this.showEventForm = false;
          this.loadEventos();
        }
      });
    } else {
      this.adminService.createEvento(this.currentEvent).subscribe(res => {
        if(res.success) {
          this.showEventForm = false;
          this.loadEventos();
        }
      });
    }
  }

  deleteEvent(id: number | undefined) {
    if(id && confirm('¿Eliminar evento?')) {
      this.adminService.deleteEvento(id).subscribe(res => {
        if(res.success) this.loadEventos();
      });
    }
  }
}
