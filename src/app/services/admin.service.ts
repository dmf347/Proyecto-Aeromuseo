import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface ReservaAdmin {
  id: number;
  usuario_id: number;
  usuario_nombre: string;
  usuario_email: string;
  fecha_visita: string;
  num_personas: number;
  comentarios: string;
  estado: string;
  created_at: string;
}

export interface EventoAdmin {
  id?: number;
  titulo: string;
  descripcion: string;
  fecha: string;
  hora: string;
  lugar: string;
  imagen_url?: string;
  activo?: number;
}

@Injectable({
  providedIn: 'root'
})
export class AdminService {
  private apiUrl = 'http://localhost/Proyecto_Angular/backend/api/admin';

  constructor(private http: HttpClient) {}

  // Reservas
  getReservas(): Observable<ReservaAdmin[]> {
    return this.http.get<ReservaAdmin[]>(`${this.apiUrl}/reservas.php`);
  }

  updateReservaStatus(id: number, estado: 'aprobada' | 'rechazada'): Observable<{success: boolean, message: string}> {
    return this.http.put<{success: boolean, message: string}>(`${this.apiUrl}/reservas.php`, { id, estado });
  }

  // Eventos
  getEventos(): Observable<EventoAdmin[]> {
    return this.http.get<EventoAdmin[]>(`${this.apiUrl}/eventos.php`);
  }

  createEvento(evento: EventoAdmin): Observable<{success: boolean, message: string}> {
    return this.http.post<{success: boolean, message: string}>(`${this.apiUrl}/eventos.php`, evento);
  }

  updateEvento(evento: EventoAdmin): Observable<{success: boolean, message: string}> {
    return this.http.put<{success: boolean, message: string}>(`${this.apiUrl}/eventos.php`, evento);
  }

  deleteEvento(id: number): Observable<{success: boolean, message: string}> {
    return this.http.delete<{success: boolean, message: string}>(`${this.apiUrl}/eventos.php?id=${id}`);
  }
}
